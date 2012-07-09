<?php

class FabricoTemplate {
	// standard classes
	const ACTIVE = 'active';
	const HIDDEN = 'hidden';
	const NOSELECT = 'noselect';

	/**
	 * @name tag
	 * @var string
	 */
	protected static $tag = 'div';

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
	protected static function pregen () {}

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

		return HTML::el(static::$tag, static::$elem);
	}
}
