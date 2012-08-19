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
	private static $callstack = array();

	/**
	 * argument stack
	 * @var array
	 */
	private static $argstack = array();

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
	public static function generate ($props = array()) {
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
	public static function open ($props = array()) {
		$klass = get_called_class();

		// save properties in call stack
		self::$callstack[] =& $props;

		// create a new argument list
		self::$argstack[] = array();

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
		return call_user_func(array('self', 'generate'), $args);
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
