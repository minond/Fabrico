<?php

namespace Fabrico;

class Element {
	/**
	 * uses for open/close tag combinations
	 *
	 * @var array
	 */
	private static $callstack = array();

	/**
	 * tag name
	 *
	 * @var string
	 */
	protected static $tag;
	
	/**
	 * tag type
	 *
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
			$props['type'] = static::$type;
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

		if (!array_key_exists($klass, self::$callstack)) {
			self::$callstack[ $klass ] = array();
		}

		// save properties in call stack
		self::$callstack[ $klass ][] =& $props;

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
		$args = array_pop(self::$callstack)[0];

		// get content
		$args['content'] = ob_get_clean();

		// generate element
		return call_user_func(array('self', 'generate'), $args);
	}

	/**
	 * optional abstract function
	 * called right before rendering the element's html
	 *
	 * @param element properties reference
	 */
	protected static function pregen (& $props) {}
}
