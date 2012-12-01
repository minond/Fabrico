<?php

/**
 * @package fabrico\cache
 */
namespace fabrico\cache;

/**
 * base cache interface
 */
abstract class Cache {
	/**
	 * item getter
	 * @param mixed $key
	 * @param mixed
	 */
	abstract public function get ($key);

	/**
	 * item setter, should return success of storage
	 * @param mixed $key
	 * @param mixed $val
	 * @return boolean
	 */
	abstract public function set ($key, $val);

	/**
	 * key checker, should return existance of key
	 * @param mixed $key
	 * @return boolean
	 */
	abstract public function has ($key);

	/**
	 * getter shortcut
	 * @see get
	 */
	public function __get ($key) {
		return $this->get($key);
	}

	/**
	 * setter shortcut
	 * @see set
	 */
	public function __set ($key, $val) {
		return $this->set($key, $val);
	}

	/**
	 * has shortcut
	 * @see has
	 */
	public function __isset ($key) {
		return $this->has($key);
	}
}
