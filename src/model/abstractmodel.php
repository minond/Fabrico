<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\cache\Cache;

/**
 * model stored in any type of cache
 */
abstract class AbstractModel {
	/**
	 * @var Cache
	 */
	protected static $cache;

	/**
	 * model's unique id
	 * @var string
	 */
	private $__id;

	/**
	 * saves model
	 */
	final public function __destruct () {
		static::initialize();
		static::$cache->set(static::hash($this->get_id()), serialize($this));
	}

	/**
	 * property getter
	 * @param string $var
	 * @return mixed
	 */
	public function __get ($var) {
		return property_exists($this, $var) ? $this->{ $var } : null;
	}

	/**
	 * property setter
	 * @param string $var
	 * @param mixed $val
	 */
	public function __set ($var, $val) {
		if (property_exists($this, $var)) {
			$this->{ $var } = $val;
		}
	}

	/**
	 * property check
	 * @param string $var
	 * @return boolean
	 */
	public function __isset ($var) {
		return property_exists($this, $var);
	}

	/**
	 * property unsetter
	 * @var string $var
	 */
	public function __unset ($var) {
		if (property_exists($this, $var)) {
			$this->{ $var } = null;
		}
	}

	/**
	 * id getter
	 * @return string
	 */
	public function get_id () {
		if (!$this->__id) {
			$this->__id = uniqid();
		}

		return $this->__id;
	}

	/**
	 * id setter
	 * @param string $id
	 */
	public function set_id ($id) {
		$this->__id = $id;
	}

	/**
	 * should start the cache
	 * called everything the cache will be used
	 */
	protected static function initialize () {}

	/**
	 * model getter
	 * @param string $id
	 * @return AbstractModel
	 */
	public static function get ($id) {
		static::initialize();
		$data = static::$cache->get(static::hash($id));
		return !is_null($data) ? unserialize($data) : null;
	}

	/**
	 * storage hash generator
	 * @param string $id
	 * @return string
	 */
	private static function hash ($id) {
		return sprintf('model-state-%s-%s', get_called_class(), $id);
	}
}
