<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\Module;
use fabrico\core\util;
use fabrico\output\Lexer;
use fabrico\output\Parser;
use fabrico\output\TagToken;
use fabrico\output\MergeToken;

/**
 * fabrico template builder
 */
class Build extends Module {
	/**
	 * @return boolean
	 */
	public function can_build () {
		return $this->configuration->core->templates->build;
	}

	/**
	 * @param array $raw
	 * @param string $build
	 * @return boolean
	 */
	public function should_build (array $raw, $build) {
		$newest = 0;

		foreach ($raw as $file) {
			$newest = max($newest, $this->fmodt($file));
		}

		return $this->fmodt($build) < $newest;
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function get_content_of ($file) {
		return file_exists($file) ? file_get_contents($file) : '';
	}

	/**
	 * @param string $file 
	 * @return int
	 */
	private function fmodt ($file) {
		return file_exists($file) ? filemtime($file) : 0;
	}

	/**
	 * @param string $file
	 * @param boolean $is_file
	 */
	private function makedir ($path, $is_file = true) {
		if ($is_file) {
			$parts = explode(DIRECTORY_SEPARATOR, $path);
			array_pop($parts);
			$path = implode(DIRECTORY_SEPARATOR, $parts);
		}

		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
	}

	/**
	 * @param string $file
	 * @param string $content
	 * @return boolean
	 */
	private function file_put ($file, $content) {
		if (!file_exists($file)) {
			$this->makedir($file);
		}

		$success = false;
		$file = fopen($file, 'w+');

		if (is_resource($file)) {
			fwrite($file, $content);
			fclose($file);
			$success = true;
		}

		return $success;
	}

	/**
	 * parses a view template
	 * @param string $content
	 * @return string
	 */
	public function std_parse ($content) {
		$this->core->loader->load('parse');
		$parser = new Parser;
		$lexer = new Lexer;
		
		$lexer->set_string($content);
		$lexer->add_token(new TagToken);
		$lexer->add_token(new MergeToken);

	}

	/**
	 * @param array $raw
	 * @param string $target
	 * @return boolean
	 */
	public function build (array $raw, $target) {
		$success = false;
		$content = '';

		foreach ($raw as $file) {
			$content .= $this->get_content_of($file);
		}

		$parsed = $this->core->response->outputcontent->prepare($content);
		return $this->file_put($target, $parsed);
	}
}
