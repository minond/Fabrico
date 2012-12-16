<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\core\LightMediator;
use fabrico\core\Router;

/**
 * loads model from a request
 */
trait WebAccess {
	use LightMediator;

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
								if (is_bool($model->{ $key })) {
									$value = (bool) $value;
								}

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
