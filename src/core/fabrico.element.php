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
	const A_CLASS = 'class';
	const A_ID = 'id';
	const A_DATA = 'data';
	const A_KEY = 'key';
	const A_NAME = 'name';
	const A_LABEL = 'label';
	const A_FORMAT = 'format';

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
	 * for unique ids
	 * @var array
	 */
	private static $class_counter = [];

	/**
	 * standard arguments
	 * @var array
	 */
	protected static $getopt = [];

	/**
	 * arguments to unset before generating element
	 * @var array
	 */
	protected static $ignore = [];

	/**
	 * unique content check
	 * @var boolean
	 */
	protected static $unique;

	/**
	 * tag name
	 * @var string
	 */
	protected static $tag = 'span';
	
	/**
	 * tag type
	 * @var string
	 */
	protected static $type;

	/**
	 * classes
	 * @var array
	 */
	protected static $classes = [];

	/**
	 * generates a new element
	 *
	 * @param array or properties
	 * @param boolean open/close tags with possbile child elements
	 * @return string element html
	 */
	public static function generate ($props = [], $has_children = false) {
		$klass = get_called_class();

		if (static::$unique === true) {

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

		// passed classes
		if (isset($props[ self::A_CLASS ]) && is_string($props[ self::A_CLASS ])) {
			$props[ self::A_CLASS ] = explode(' ', $props[ self::A_CLASS ]);
		}
		else {
			$props[ self::A_CLASS ] = [];
		}

		if (!isset($props[ self::A_ID ])) {
			$props[ self::A_ID ] = self::gen_id();
		}

		// check for new-mode pregen function
		if (count(static::$getopt)) {
			$props = (object) $props;

			foreach (static::$getopt as $opt) {
				if (!property_exists($props, $opt)) {
					$props->{ $opt } = null;
				}
			}

			$build = static::pregen($props);
			$props = (array) $props;

			if (count(static::$ignore)) {
				foreach (static::$ignore as $ignore) {
					if (isset($props[ $ignore ])) {
						unset($props[ $ignore ]);
					}
				}
			}
		}
		else {
			$build = static::pregen($props);
		}

		$props[ self::A_CLASS ] += static::$classes;
		$props[ self::A_CLASS ] = implode(' ', $props[ self::A_CLASS ]);

		if (!$props[ self::A_CLASS ]) {
			unset($props[ self::A_CLASS ]);
		}

		if ($has_children) {
			Arbol::closing($klass, $props[ self::A_ID ], static::$tag, $props);
		}
		else {
			Arbol::child($klass, $props[ self::A_ID ], static::$tag, $props);
		}

		if ($build !== false && static::$tag !== false) {
			return trim(html::generate(static::$tag, $props));
		}
		else {
			return '';
		}
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

		// tree structure
		Arbol::opening($klass);

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
		$args[ self::A_CONTENT ] = trim(ob_get_clean());

		// generate element
		return call_user_func_array([ 'self', 'generate' ], [ $args, true ]);
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
	 * returns a unique id
	 *
	 * @return string unique element id
	 */
	private static function gen_id () {
		$klass = get_called_class();

		if (!isset(self::$class_counter[ $klass ])) {
			self::$class_counter[ $klass ] = 0;
		}

		return Merge::parse('#{class}_#{count}_#{session}', [
			'class' => preg_replace('/\W/', '_', $klass),
			'count' => ++self::$class_counter[ $klass ],
			'session' => session_id()
		]);
	}

	/**
	 * optional abstract function
	 * called right before rendering the element's html
	 *
	 * @param element properties reference
	 */
	protected static function pregen (& $props) {}
}
