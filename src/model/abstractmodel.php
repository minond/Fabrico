<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\cache\Cache;

/**
 * model stored in any type of cache
 */
abstract class AbstractModel extends Model {
	/**
	 * @var Cache
	 */
	protected static $cache;

	/**
	 * model's unique id
	 * @var string
	 */
	private $id;

	/**
	 * initializes model
	 */
	public function __construct () {
		static::initialize();
	}

	/**
	 * saves model
	 */
	final public function __destruct () {
		static::initialize();
		static::$cache->set(
			static::hash($this->get_id()), serialize($this)
		);
	}

	/**
	 * id getter
	 * @return string
	 */
	public function get_id () {
		if (!$this->id) {
			$this->id = uniqid();
		}

		return $this->id;
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
