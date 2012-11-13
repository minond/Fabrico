<?php

/**
 * @package fabrico\controller
 */
namespace fabrico\controller;

use fabrico\core\util;
use fabrico\core\Project;
use fabrico\core\Module;
use fabrico\core\Request;
use fabrico\core\Router;

/**
 * controller base
 */
class Controller extends Module {
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
	 * load and setup a new controller
	 * @param string $controller
	 * @param boolean $save
	 * @return Controller
	 */
	public static function load ($controller, $save = true) {
		$core = & self::getcore();
		$file = $core->project->get_file($controller, Project::CONTROLLER);

		require_once $file;
		$controller = new $controller;

		if ($save) {
			$core->controller = $controller;
		}

		return $controller;
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
	public static function trigger_method (Controller $controller, Request $req) {
		$method = $req->get(Router::$var->method);
		$return = null;

		if ($controller->published($method)) {
			$arguments = $req->get(Router::$var->args);
			$arguments = is_array($arguments) ? $arguments : [];
			$return = call_user_func_array(array($controller, $method), $arguments);
		}

		return $return;
	}




	/* testing */
	public $name;
	public function __construct () {
		$this->name = get_class($this);
	}
	public function name () { return "~~{$this->name}"; }
	/* testing */
}
