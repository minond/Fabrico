<?php

/**
 * @package fabrico\cache
 */
namespace fabrico\cache;

/**
 * Run time memory
 */
class RuntimeMemory implements Cache {
	private static $mem = [];

	/**
	 * @see Cache::get
	 */
	public function get ($key) {
		return $this->has($key) ? self::$mem[ $key ] : null;
	}

	/**
	 * @see Cache::set
	 */
	public function set ($key, $val) {
		self::$mem[ $key ] = $val;
		return true;
	}

	/**
	 * @see Cache::has
	 */
	public function has ($key) {
		return isset(self::$mem[ $key ]);
	}
}
