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
	 * arguments to be passed to requested function
	 * @see self::load_cli_function_arguments
	 * @var array
	 */
	private $__function_arguments = [];

	/**
	 * parses options
	 * @param array $all
	 * @return array
	 */
	private function short_longs($all) {
		$short = [];
		$longs = [];
		$options = [];

		foreach ($all as $prop) {
			// `private` vars
			if (substr($prop, 0, 2) === '__') {
				continue;
			}

			$options[ "{$prop}::" ] = $prop;
			$options[ "{$prop[0]}::" ] = $prop;
	}

		foreach ($options as $arg => $variable) {
			if (strlen($this->clean_argument($arg)) === 1) {
				$short[] = $arg;
			}
			else {
				$longs[] = $arg;
			}
		}

		return [ $short, $longs, $options ];
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

	/**
	 * uses controller properties to parse arguments
	 */
	public function load_cli_property_arguments() {
		list($short, $longs, $options) = $this->short_longs(
			array_keys(get_class_vars(get_class($this)))
		);

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
	 * uses function parameters to parse arguments
	 */
	public function load_cli_function_arguments($func) {
		global $argv;

		$re = new \ReflectionMethod(get_class($this), $func);
		$arg_offset = 4;
		$args = [];

		foreach ($re->getParameters() as $index => $par) {
			$args[ $par->getName() ] = null;

			if ($par->isDefaultValueAvailable()) {
				$args[ $par->getName() ] = $par->getDefaultValue();
			}
		}

		list($short, $longs, $options) = $this->short_longs(array_keys($args));
		$parsed = getopt(implode('', $short), $longs);
		$index = -1;

		foreach ($args as $name => $def_value) {
			$index++;

			$this->__function_arguments[ $index ] = array_key_exists($name, $parsed) ?
				$parsed[ $name ] : null;

			if (!strlen($this->__function_arguments[ $index ])) {
				$this->__function_arguments[ $index ] = $def_value;
			}

			if (is_null($this->__function_arguments[ $index ])) {
				if (isset($argv[ $index + $arg_offset ])) {
					$this->__function_arguments[ $index ] = $argv[ $index + $arg_offset ];
				}
			}
		}
	}

	/**
	 * arguments getter
	 * @return array
	 */
	public function get_function_arguments() {
		return $this->__function_arguments;
	}
}
