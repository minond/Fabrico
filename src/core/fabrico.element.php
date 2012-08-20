<?php

namespace Fabrico;

class Element {
	/**
	 * standard properties
	 */
	const A_PARAM = 'params';
	const A_CONTENT = 'content';
	const A_TYPE = 'type';
	const A_NULL = 'null';

	/**
	 * uses for open/close tag combinations
	 * @var array
	 */
	private static $callstack = [];

	/**
	 * argument stack
	 * @var array
	 */
	private static $argstack = [];

	/**
	 * unique content check
	 * @var array
	 */
	private static $unique_content = [];

	/**
	 * unique content check
	 * @var boolean
	 */
	protected static $unique;

	/**
	 * tag name
	 * @var string
	 */
	protected static $tag;
	
	/**
	 * tag type
	 * @var string
	 */
	protected static $type;

	/**
	 * generates a new element
	 *
	 * @param array or properties
	 * @return string element html
	 */
	public static function generate ($props = []) {
		if (static::$unique === true) {
			$klass = get_called_class();

			if (!array_key_exists($klass, self::$unique_content)) {
				self::$unique_content[ $klass ] = [];
			}

			if (isset($props[ self::A_CONTENT ])) {
				if (in_array($props[ self::A_CONTENT ], self::$unique_content[ $klass ])) {
					return '';
				}
				else {
					self::$unique_content[ $klass ][] = $props[ self::A_CONTENT ];
				}
			}
		}

		if (static::$type) {
			$props[ self::A_TYPE ] = static::$type;
		}

		return static::pregen($props) !== false ?
		       html::generate(static::$tag, $props) : '';
	}

	/**
	 * saves element properties and starts content buffer capture
	 *
	 * @param array or properties
	 */
	public static function open ($props = []) {
		$klass = get_called_class();

		// save properties in call stack
		self::$callstack[] =& $props;

		// create a new argument list
		self::$argstack[] = [];

		// save buffer
		ob_start();
	}

	/**
	 * closes the content buffer and pops the last openeded
	 * element off the call stack
	 *
	 * @return string element html
	 */
	public static function close () {
		// get arguments
		$args = array_pop(self::$callstack);

		// parameters
		$args[ self::A_PARAM ] = array_pop(self::$argstack);

		// get content
		$args[ self::A_CONTENT ] = ob_get_clean();

		// generate element
		return call_user_func([ 'self', 'generate' ], $args);
	}

	/**
	 * saves a parameter in the argument stack
	 *
	 * @param mixed value
	 */
	public static function argument ($value) {
		self::$argstack[ count(self::$argstack) - 1 ][] = $value;
	}

	/**
	 * optional abstract function
	 * called right before rendering the element's html
	 *
	 * @param element properties reference
	 */
	protected static function pregen (& $props) {}
}
