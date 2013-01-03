<?php

use fabrico\core\Core;
use fabrico\controller\Controller;
use fabrico\controller\CliAccess as Cli;
use fabrico\cli\CliSelfDocumenting;
use fabrico\cli\CliArgLoader;
use fabrico\cli\CliIo;
use fabrico\project\Project;
use fabrico\fs\FileAccess;
use fabrico\fs\Cache as FileCache;

// needs file helpers
Core::load('fs');

class Help extends Controller implements Cli {
	use CliSelfDocumenting, CliArgLoader, CliIo, FileCache;

	/**
	 * documentation cache file
	 */
	const DOC_CACHE = 'documentation';

	/**
	 * list of controller directories
	 * @var array
	 */
	public $cdirs = [];

	/**
	 * @var FileAccess
	 */
	public $fsa;

	public function __construct() {
		$this->fsa = new FileAccess($this->core->project);
		$this->cdirs = [
			$this->core->project->get_root() .
				$this->configuration->core->directory->controllers,
			$this->core->project->get_myroot() .
				$this->configuration->core->directory->controllers,
		];
	}

	/**
	 * @param array $funcs
	 * @return string
	 */
	private function compile_document(array $funcs) {
		$lines = [];
		$count = 0;
		$padding_len = 0;
		$padding = '';
		$small_padding = '  ';
		$ruler = implode('', array_map(function() {
			return '-';
		}, range(1, $this->get_cli_dims()->cols)));

		foreach ($funcs as $controller => $info) {
			$info = (array) $info;

			foreach ($info['functions'] as $fn => $data) {
				$padding_len = max($padding_len, strlen($fn) + 10);
			}
		}

		$padding = implode('', array_map(function() {
			return ' ';
		}, range(1, $padding_len)));

		foreach ($funcs as $controller => $info) {
			if ($count++) {
				$lines[] = '';
			}

			$info = (array) $info;
			$lines[] = 'fabrico ' .
				$this->cbold(strtolower($controller)) .
				' [<args>]';
			// $lines[] = $ruler;

			foreach ($info['functions'] as $fname => $function) {
				$function = (array) $function;
				$params = [];
				$comment = $function['comment'];

				foreach ($function['params'] as $param) {
					$param = (array) $param;
					$params[] = sprintf(
						'[--%s=<%s>]',
						$this->cfblue(substr($param['name'], 1)),
						$param['type']
					);
				}

				$params = implode(' ', $params);

				if (strlen($comment) && strlen($params)) {
					$comment = $comment . PHP_EOL;
					$params = $small_padding . $padding . $params;
				}

				$lines[] = implode('', array_filter([
					$small_padding,
					str_pad($fname, $padding_len),
					$comment, $params
				]));
			}
		}

		return implode(PHP_EOL, $lines) . PHP_EOL;
	}

	/**
	 * generate this help content
	 */
	public function generate() {
		$clic = [];
		$docs = [];

		$access = 'CliAccess';
		$mangen = 'CliSelfDocumenting';

		$this->cout('searching for cli controllers:');
		foreach ($this->cdirs as $dir) {
			foreach ($this->fsa->files_in($dir, '/\.php/') as $controller) {
				if ($this->fsa->contains($access, $controller)) {
					$clic[] = [
						$controller,
						$this->fsa->with_path($controller),
						$this->fsa->contains($mangen, $controller)
					];

					$this->cout(" {$this->cfgreen($this->checkmark)} {$controller}");
				}
				else {
					$this->cout(" {$this->cfred($this->crossmark)} {$controller}");
				}
			}

		}

		if (count($clic)) {
			$this->cout("\nfound %s items, generating documentation:", count($clic));

			foreach ($clic as $info) {
				list($controller, $file, $selfdoc) = $info;
				$controller = str_replace(
					$this->core->project->ext(Project::CONTROLLER),
					'', $controller);

				$instance = null;
				$functions = [];

				$this->cout(" {$this->cbold($controller)}");

				if ($selfdoc) {
					$this->cout(" {$this->checkmark} loading...");
					require_once $file;

					$this->cout(" {$this->checkmark} instanciating...");
					$instance = new $controller;

					$this->cout(" {$this->checkmark} parsing functions...");
					$functions = $instance->generate_man_functions();

					$this->cout(" {$this->checkmark} saving...");
					$docs[ $controller ] = [ 'functions' => $functions ];

					$this->cout(" {$this->checkmark} cleaning up...");
					$instance = null;
					$functions = null;
					$functions = [];
				}
				else {
					$this->cout(" {$this->crossmark} not a seld documenting controller, skipping");
				}

				$this->cout('');
			}

			$this->cout(
				'documentation generated and saved to %s',
				$this->jcsave(self::DOC_CACHE, $docs)
			);
		}
		else {
			$this->cout("\nno cli controllers found");
		}

		return $docs;
	}

	/**
	 * view this help content
	 */
	public function trigger() {
		$docs = $this->jcread(self::DOC_CACHE);

		if ($docs === false) {
			$docs = $this->generate();
			echo PHP_EOL, PHP_EOL, PHP_EOL;
		}

		echo $this->compile_document((array) $docs);
	}
}
