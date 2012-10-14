<?php

namespace Fabrico;

class Std {
	/**
	 * returns the controller method name
	 */
	public static function get_controller_view_method () {
		$parts = explode('/', Core::$configuration->state->uri);
		$parts[0] = 'action';

		return implode('_', $parts);
	}

	/**
	 * checks if a controller has a method to handle the requeted view
	 *
	 * @param Controller
	 */
	public static function controller_has_view_method (& $controller) {
		$method = self::get_controller_view_method();
		return method_exists($controller, $method) && in_array($method, $controller->public);
	}
};
