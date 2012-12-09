<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\cache\Cache;
use fabrico\core\LightMediator;
use fabrico\core\Router;

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

	/**
	 * look for and load a model passed through the router
	 * @return Model
	 */
	public static function checkload() {
		$router = static::getcore()->router;
		$model = null;

		if ($router instanceof Router) {
			if ($router->get(self::WEB_PASS) && $router->get(self::WEB_NAME) === get_called_class()) {
				$model = $router->get(self::WEB_NAME);

				if (class_exists($model)) {
					$model = $model::get($router->get('id'));

					if (is_null($model)) {
						$model = new static;
					}

					if ($model) {
						foreach ($router->gets() as $key => $value) {
							if (property_exists($model, $key)) {
								if (method_exists($model, "set_{$key}")) {
									$model->{"set_{$key}"}($value);
								}
								else {
									$model->{ $key } = $value;
								}
							}
						}

						$model->__destruct();
					}
				}
			}
		}

		return $model;
	}
}
