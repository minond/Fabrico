<?php

/**
 * @package fabrico\loader
 */
namespace fabrico\loader;

/**
 * auto loader
 */
class Loader {
	/**
	 * formatter function name convention
	 */
	const FORMATTER = '%s_format';

	/**
	 * @var array
	 */
	protected $files = [];

	/**
	 * files to load right away
	 * @var array
	 */
	protected $autoload = [];

	/**
	 * base path
	 * @var string
	 */
	protected $path_to = '';

	/**
	 * trigger the autoload
	 */
	public function __construct () {
		foreach ($this->autoload as $ns) {
			$this->load($ns);
		}
	}

	/**
	 * @see self::load
	 */
	public function __invoke() {
		call_user_func_array([ $this, 'load' ], func_get_args());
	}

	/**
	 * @param string $namespace
	 * @param array $files
	 */
	protected function register ($namespace, $files) {
		$this->files[ $namespace ] = $files;
	}

	/**
	 * loads files
	 * @param string $namespace
	 */
	public function load ($namespace) {
		foreach (func_get_args() as $namespace) {
			$this->load_ns($namespace);
		}
	}

	/**
	 * loads all files in a namespace
	 * @param string $namespace
	 */
	private function load_ns ($namespace) {
		if (array_key_exists($namespace, $this->files)) {
			foreach ($this->files[ $namespace ] as $file) {
				require_once $this->format_file($file, $namespace);
			}
		}
	}

	/**
	 * loads all files in all namespaces
	 */
	private function load_all () {
		foreach ($this->files as $namespace => $files) {
			$this->load_ns($namespace);
		}
	}

	/**
	 * @param string $file
	 * @param string $namespace
	 * @return string
	 */
	private function format_file ($file, $namespace) {
		$fn = sprintf(self::FORMATTER, $namespace);

		if (method_exists($this, $fn)) {
			$file = $this->{ $fn }($file);
		}

		return $file;
	}

	/**
	 * path setter
	 * @param string $path
	 */
	public function set_path ($path) {
		$this->path_to = $path;
	}

	/**
	 * path getter
	 * @return string
	 */
	public function get_path () {
		return $this->path_to;
	}
}
