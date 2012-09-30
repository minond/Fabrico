<?php

namespace fabrico;

class AutoLoader {
	/**
	 * @var array
	 */
	protected $files = [];

	/**
	 * @var array
	 */
	protected $formats = [];

	/**
	 * @var string $namespace
	 * @var array $files
	 */
	protected function register ($namespace, $files) {
		$this->files[ $namespace ] = $files;
	}

	/**
	 * @var string $namespace
	 * @var callable $format
	 */
	protected function format ($namespace, callable $format) {
		$this->formats[ $namespace ] = $format;
	}

	/**
	 * loads files
	 * @param string $namespace
	 */
	public function load ($namespace) {
		foreach ($this->files[ $namespace ] as $file) {
			$file = array_key_exists($namespace, $this->formats) ?
			        $this->formats[ $namespace ]($file) : $file;

			require_once $file;
		}
	}
}
