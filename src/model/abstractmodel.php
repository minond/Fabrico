<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\core\LightMediator;
use fabrico\output\Json;

/**
 * model stored in any type of cache
 */
abstract class AbstractModel extends Model {
	use LightMediator;

	/**
	 * model request variables
	 */
	const WEB_PASS = 'model_save';
	const WEB_NAME = 'model_name';

	/**
	 * @var Cache
	 */
	protected static $cache;

	/**
	 * model's unique id
	 * @var string
	 */
	protected $id;

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
			static::hash($this->get_id()),
			serialize($this)
		);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$me = get_class($this);
		$props = [];

		foreach ($this as $prop => $value) {
			$props[] = sprintf('%s:%s="%s"', $me, $prop, htmlspecialchars($value));
		}

		$props = implode(' ', $props);
		return "<model:{$me} {$props} />";
	}

	/**
	 * @return string
	 */
	public function as_json() {
		static::getcore()->loader->load('output');
		$json = new Json;

		foreach ($this as $prop => $value) {
			$json->{ $prop } = $value;
		}

		return (string) $json;
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
	 * storage hash generator
	 * @param string $id
	 * @return string
	 */
	private static function hash ($id) {
		return sprintf('model-state-%s-%s', get_called_class(), $id);
	}

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
}
