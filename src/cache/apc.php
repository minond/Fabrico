<?php

/**
 * @package fabrico\cache
 */
namespace fabrico\cache;

/**
 * Apc class interface
 */
class Apc extends Cache {
	/**
	 * @see Cache::get
	 */
	public function get ($key) {
		return $this->has($key) ? apc_fetch($key) : null;
	}

	/**
	 * @see Cache::set
	 */
	public function set ($key, $val) {
		return apc_add($key, $val);
	}

	/**
	 * @see Cache::has
	 */
	public function has ($key) {
		return apc_exists($key);
	}
}
