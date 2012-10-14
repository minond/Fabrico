<?php

namespace fabrico;

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
	 * @var array
	 */
	protected $formats = [];

	/**
	 * acts as an auto format setter
	 */
	public function __construct () {
		foreach ($this->files as $ns => $files) {
			$fn = sprintf(self::FORMATTER, $ns);

			if (method_exists($this, $fn)) {
				$this->format($ns, [ $this, $fn ]);
			}
		}
	}

	/**
	 * @param string $namespace
	 * @param array $files
	 */
	protected function register ($namespace, $files) {
		$this->files[ $namespace ] = $files;
	}

	/**
	 * @param string $namespace
	 * @param callable $format
	 */
	protected function format ($namespace, callable $format) {
		$this->formats[ $namespace ] = $format;
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
		return !array_key_exists($namespace, $this->formats) ? $file :
		       call_user_func($this->formats[ $namespace ], $file);
	}
}
