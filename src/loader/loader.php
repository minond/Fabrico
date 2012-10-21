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
	public function load ($namespace = false) {
		$namespace ? $this->load_ns($namespace) : $this->load_all();
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
}
