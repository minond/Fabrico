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
			// project controller? otherwise, internal controller
			if (self::getcore()->project->has_file($controller, Project::CONTROLLER)) {
				$controller = self::getcore()->configuration->core->project->namespace .
					self::getcore()->configuration->core->namespace->controllers .
					$controller;
			}

			$instance = new $controller;

			if ($save) {
				self::getcore()->controller = $instance;
			}
		}

		return $instance;
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
			$args = $controller->get_function_arguments();
			return call_user_func_array([ $controller, $method ], $args);
		}
		else {
			throw new \Exception(sprintf(
				'Invalid method "%s" for "%s" controller%s',
				$method, get_class($controller), PHP_EOL
			));
		}
	}
}
