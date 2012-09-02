<?php

namespace Fabrico;

class Element {
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
	public static function generate ($props, $has_children = false) {
		$klass = get_called_class();

		if (static::$unique === true) {
			if (!array_key_exists($klass, self::$unique_content)) {
				self::$unique_content[ $klass ] = [];
			}

			if (isset($props->content)) {
				if (in_array($props->content, self::$unique_content[ $klass ])) {
					return '';
				}
				else {
					self::$unique_content[ $klass ][] = $props->content;
				}
			}
		}

		if (static::$type) {
			$props->type = static::$type;
		}

		// passed classes
		if (isset($props->class) && is_string($props->class)) {
			$props->class = explode(' ', $props->class);
		}
		else {
			$props->class = [];
		}

		if (!isset($props->id)) {
			$props->id = self::gen_id();
		}

		// check for new-mode pregen function
		if (count(static::$getopt)) {
			foreach (static::$getopt as $opt) {
				if (!property_exists($props, $opt)) {
					$props->{ $opt } = null;
				}
			}

			$build = static::pregen($props);

			if (count(static::$ignore)) {
				foreach (static::$ignore as $ignore) {
					if (isset($props->{ $ignore })) {
						unset($props->{ $ignore });
					}
				}
			}
		}
		else {
			$build = static::pregen($props);
		}

		$props->class += static::$classes;
		$props->class = implode(' ', $props->class);

		if (!$props->class) {
			unset($props->class);
		}

		if ($has_children) {
			Arbol::closing($klass, $props->id, static::$tag, $props);
		}
		else {
			Arbol::child($klass, $props->id, static::$tag, $props);
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
	public static function open ($props = false) {
		$klass = get_called_class();

		if (!$props) {
			$props = new \stdClass;
		}

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
		$args->param = array_pop(self::$argstack);

		// get content
		$args->content = trim(ob_get_clean());

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
