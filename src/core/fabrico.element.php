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
	 * treat element as a parameter
	 * @var boolean
	 */
	protected static $parameter = false;
	
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
	 * elements to load
	 * @var array
	 */
	protected static $elements = [];

	/**
	 * scripts to load
	 * @var array
	 */
	protected static $scripts = [];

	/**
	 * css to load
	 * @var array
	 */
	protected static $styles = [];

	/**
	 * parses a class name for namespace and tag information
	 *
	 * @param string class name
	 * @return array
	 */
	private static function parse_class_name ($klass) {
		$info = new \stdClass;
		$parts = explode('\\', $klass);
		array_shift($parts);

		$info->namespace = $parts[ 0 ];
		$info->name = $parts[ 1 ];
		$info->class = implode('_', $parts);

		return $info;
	}

	/**
	 * generates a new element
	 *
	 * @param array or properties
	 * @param boolean open/close tags with possbile child elements
	 * @return string element html
	 */
	public static function generate ($props = [], $has_children = false) {
		$build = false;
		$klass = get_called_class();

		if (is_array($props)) {
			$props = (object) $props;
		}

		// load elements
		if (count(static::$elements)) {
			foreach (static::$elements as $element) {
				\view\element($element);
			}
		}

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
		}

		// passed classes
		if (isset($props->class) && is_string($props->class)) {
			$props->class = explode(' ', $props->class);
		}
		else {
			$props->class = [];
		}

		if (!static::$parameter) {
			$build = static::pregen($props);
		}
		else {
			$klass_info = self::parse_class_name($klass);
			$props->namespace = $klass_info->namespace;
			$props->tagname = $klass_info->name;
			$props->classname = $klass_info->class;
			self::argument($props);
		}

		if (count(static::$ignore)) {
			foreach (static::$ignore as $ignore) {
				if (isset($props->{ $ignore })) {
					unset($props->{ $ignore });
				}
			}
		}

		$props->class = array_merge(static::$classes, $props->class);
		$props->class = implode(' ', $props->class);

		if (!$props->class) {
			unset($props->class);
		}

		// child hierarchy
		if ($has_children) {
			Arbol::closing($klass, $props->id, static::$tag, $props);
		}
		else {
			Arbol::child($klass, $props->id, static::$tag, $props);
		}

		// scripts
		if (count(static::$scripts)) {
			foreach (static::$scripts as $script) {
				\view\resource\script::generate([
					'src' => is_array($script) ? $script[ 0 ] : $script,
					'core' => is_array($script)
				]);
			}
		}

		// styles
		if (count(static::$styles)) {
			foreach (static::$styles as $style) {
				\view\resource\style::generate([
					'href' => is_array($style) ? $style[ 0 ] : $style,
					'core' => is_array($style)
				]);
			}
		}

		// to html
		return $build !== false && static::$tag !== false ?
		       trim(html::generate(static::$tag, $props)) : '';
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
	 * parameter search
	 * 
	 * @param string $classname
	 * @return array
	 */
	public static function param_get ($classname, & $params) {
		$ret = array();

		foreach ($params as & $param) {
			if ($param->classname === $classname) {
				$ret[] = & $param;
			}

			unset($param);
		}

		return $ret;
	}

	/**
	 * param_get short-cut
	 *
	 * @param object $params
	 * @return array
	 */
	public static function search (& $params) {
		$klass_info = self::parse_class_name(get_called_class());
		return self::param_get($klass_info->class, $params->param);
	}

	/**
	 * search short-cut
	 *
	 * @param object $param
	 * @return Element
	 */
	public static function first (& $params) {
		$ret = static::search($params);
		return count($ret) ? $ret[ 0 ] : false;
	}

	/**
	 * template merger
	 *
	 * @param string $template
	 * @param mixed array|object $data
	 * @return string
	 */
	public static function merge ($template, $data) {
		return Merge::parse($template, $data, Merge::PLACEHOLDER_SELECTOR);
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
