<?php

/**
 * @package fabrico\cache
 */
namespace fabrico\cache;

/**
 * base cache interface
 */
interface Cache {
	/**
	 * item getter
	 * @param mixed $key
	 * @param mixed
	 */
	public function get ($key);

	/**
	 * item setter, should return success of storage
	 * @param mixed $key
	 * @param mixed $val
	 * @return boolean
	 */
	public function set ($key, $val);

	/** 
	 * key checker, should return existance of key
	 * @param mixed $key
	 * @return boolean
	 */
	public function has ($key);
}
