<?php

/**
 * @package fabrico\controller
 */
namespace fabrico\controller;

use fabrico\core\util;
use fabrico\core\Module;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\project\Project;
use fabrico\project\FileFinder;
use fabrico\project\FileLoader;

/**
 * controller base
 */
class Controller extends Module implements FileFinder {
	use FileLoader;

	/**
	 * array of public methods
	 * @var array
	 */
	private $public_methods = [];

	/**
	 * public method adder
	 * @param string $method
	 */
	protected function publish ($method) {
		$this->public_methods[] = $method;
	}

	/**
	 * published method checker
	 * @param string $method
	 * @return boolean
	 */
	public function published ($method) {
		return in_array($method, $this->public_methods);
	}

	/**
	 * for FileFinder
	 */
	public static function get_project_file_type() {
		return Project::CONTROLLER;
	}

	/**
	 * load and setup a new controller
	 * @param string $controller
	 * @param boolean $save
	 * @return Controller
	 */
	public static function load($controller, $save = true) {
		$instance = null;

		if (self::load_project_file($controller)) {
			$instance = new $controller;

			if ($save) {
				self::getcore()->controller = $instance;
			}
		}

		return $instance;
	}

	/**
	 * load a controller from a request
	 * @param Request $req
	 * @return Controller
	 */
	public static function req_load (Request $req) {
		return self::load($req->get(Router::$var->controller), false);
	}

	/**
	 * is method callable?
	 * @param Controller $controller
	 * @param Request $req
	 * @return boolean
	 */
	public static function request_status (Controller $controller, Request $req) {
		return $controller->published($req->get(Router::$var->method));
	}

	/**
	 * trigger method
	 * @param Controller $controller
	 * @param Request $req
	 * @return mixed
	 */
	public static function trigger_web_request (Controller $controller, Request $req) {
		$method = $req->get(Router::$var->method);
		$return = null;

		if ($controller->published($method)) {
			$arguments = $req->get(Router::$var->args);
			$arguments = is_array($arguments) ? $arguments : [];
			$return = call_user_func_array(array($controller, $method), $arguments);
		}

		return $return;
	}

	/**
	 * @param Controller $controller
	 * @param string $method
	 * @return mixed
	 */
	public static function trigger_cli_request (Controller $controller, $method) {
		if (is_callable([$controller, $method])) {
			return $controller->{ $method }();
		}
		else {
			echo sprintf(
				'Invalid method "%s" for "%s" controller%s',
				$method, get_class($controller), PHP_EOL
			);
		}
	}
}
