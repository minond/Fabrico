<?php

/**
 * @package fabrico\cli;
 */
namespace fabrico\cli;

/**
 * loads command arguments into controller properties
 */
trait CliArgLoader {
	/**
	 * uses controller properties to parse arguments
	 */
	public function load_cli_arguments() {
		static $parsed;
		$short = [];
		$longs = [];

		if (!$parsed) {
			$parsed = true;
		}
		else {
			return;
		}

		$options = isset($this->options) ? $this->options : false;

		if (!$options) {
			foreach (array_keys(get_class_vars(get_class($this))) as $prop) {
				$options[ "{$prop}::" ] = $prop;
				$options[ "{$prop[0]}::" ] = $prop;
			}
		}

		foreach ($options as $arg => $variable) {
			if (strlen($this->clean_argument($arg)) === 1) {
				$short[] = $arg;
			}
			else {
				$longs[] = $arg;
			}
		}

		foreach (getopt(implode('', $short), $longs) as $param => $value) {
			foreach ($options as $arg => $variable) {
				if ($this->clean_argument($arg) === $param) {
					$this->{ $variable } = $value;
					break;
				}
			}
		}
	}

	/**
	 * returns the argument name without options information
	 * @param string $arg
	 * @return string
	 */
	private function clean_argument($arg) {
		static $cache;

		if (!$cache) {
			$cache = [];
		}

		if (!array_key_exists($arg, $cache)) {
			$cache[ $arg ] = preg_replace('/:+$/', '', $arg);
		}

		return $cache[ $arg ];
	}
}
