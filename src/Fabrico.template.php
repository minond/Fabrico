<?php

class FabricoTemplate {
	// standard classes
	const ACTIVE = 'active';
	const HIDDEN = 'hidden';
	const NOSELECT = 'noselect';

	/**
	 * @name prehtml
	 * @var string
	 */
	protected static $prehtml = '';

	/**
	 * @name posthtml
	 * @var string
	 */
	protected static $posthtml = '';

	/**
	 * @name onready
	 * @var string
	 */
	public static $onready;

	/**
	 * @name onready_vars
	 * @var array
	 */
	protected static $onready_vars = array();

	/**
	 * @name tag
	 * @var string
	 */
	protected static $tag = 'div';

	/**
	 * @name noclose
	 * @var boolean
	 */
	protected static $noclose = false;

	/**
	 * @name type
	 * @var string
	 */
	protected static $type;

	/**
	 * @name elem
	 * @var stdClass element
	 */
	protected static $elem;

	/**
	 * @name id_delim
	 * @var string
	 */
	public static $id_delim = '_';

	/**
	 * @name salt
	 * @var string
	 */
	protected static $salt;
	
	/**
	 * @name pepper
	 * @var string
	 */
	protected static $pepper;
	
	/**
	 * @name class
	 * @var array
	 */
	protected static $class = array();

	/**
	 * @name pregen
	 * @virtual
	 */
	protected static function pregen ($content) {
		static::$elem->content = $content;
	}

	/**
	 * @name is_active
	 * @param boolean
	 * @return boolean
	 */
	protected static function is_active ($active) {
		if ($active) {
			static::$elem->class[] = self::ACTIVE;
		}
	}

	/**
	 * @name gen_id
	 * @param string middle
	 * @return string id
	 */
	public static function gen_id ($id) {
		return implode(
			static::$id_delim,
			array(static::$salt, self::gen_name($id), static::$pepper)
		);
	}

	/**
	 * @name gen_name
	 * @param string name
	 * @return string clean name
	 */
	public static function gen_name ($name) {
		return preg_replace('/\W/', '', strtolower($name));
	}

	/**
	 * @name handle_code
	 */
	protected static function handle_code () {
		$onready_copy = static::$onready;

		foreach (static::$onready_vars as $key => $value) {
			$onready_copy = preg_replace(
				"/%{$key}/", $value,
				$onready_copy
			);
		}

		Resource::onready($onready_copy);
	}

	/**
	 * @name salt_n_pepper
	 */
	public static function salt_n_pepper () {
		$name = get_called_class();
		$parts = preg_split('/_/', $name);

		if (count($parts) >= 2) {
			static::$salt = $parts[0];
			unset($parts[ 0 ]);
			static::$pepper = implode('_', $parts);
		}
		else {
			static::$salt = $name;
		}
	}

	/**
	 * @name gen
	 * @return string html
	 */
	public static function gen () {
		static::$elem = new stdClass;
		static::$elem->class = array();
		static::$onready_vars = array();
		static::$prehtml = '';
		static::$posthtml = '';

		if (!static::$salt) {
			static::salt_n_pepper();
		}

		call_user_func_array(
			array('static', 'pregen'),
			func_get_args()
		);

		static::$elem->class = implode(' ', 
			array_merge(static::$class, static::$elem->class)
		);

		if (static::$type) {
			static::$elem->type = static::$type;
		}

		static::handle_code();
		return static::$prehtml . HTML::el(static::$tag, static::$elem, static::$noclose) . static::$posthtml;
	}
}
