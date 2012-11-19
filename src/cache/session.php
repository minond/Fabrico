<?php

/**
 * @package fabrico\cache
 */
namespace fabrico\cache;

/**
 * Session class interface
 */
class Session implements Cache {
	/**
	 * @see Cache::get
	 */
	public function get ($key) {
		return $this->has($key) ? $_SESSION[ $key ] : null;
	}

	/**
	 * @see Cache::set
	 */
	public function set ($key, $val) {
		$_SESSION[ $key ] = $val;
		return true;
	}

	/**
	 * @see Cache::has
	 */
	public function has ($key) {
		return isset($_SESSION[ $key ]);
	}
}
