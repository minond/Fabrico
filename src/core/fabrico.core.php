<?php

namespace Fabrico;

class Core {
	/**
	 * core dependancies
	 *
	 * @var array
	 */
	public static $deps = array(
		'../deps/sfYaml/sfYaml.php'
	);

	/**
	 * path to framework configuration
	 */
	public static $configuration_path = '../../configuration/configuration.yml';

	/**
	 * holds framework and project configuration
	 *
	 * @var object
	 */
	public static $configuration;

	/**
	 * loads framework configuration
	 */
	public static function load_core_configuration () {
		self::$configuration = (object) \sfYaml::load(self::$configuration_path);
		self::$configuration->state = new \stdClass();
		self::$configuration->loading = (object) self::$configuration->loading;
		self::$configuration->directory = (object) self::$configuration->directory;
		self::$configuration->convention = (object) self::$configuration->convention;
	}

	/**
	 * initializes needed variables and modules
	 *
	 * @param array request object
	 */
	public static function load_core_setup (& $req) {
		Router::set_request($req);
		Project::set_files();
	}

	/**
	 * loads core dependancies
	 */
	public static function load_core_dependancies () {
		foreach (self::$deps as $dep) {
			require_once $dep;
		}
	}

	/**
	 * handles current request loading views and controllers
	 */
	public static function handle_request () {
		$controller_info = self::$configuration->state->controller;

		// load controller
		if ($controller_info->file_path) {
			require_once $controller_info->file_path;
		}

		// and instanciate it
		$controller = "\\{$controller_info->controller_name}";
		$controller = new $controller;

		switch (Router::request_method()) {
			case Router::VIEW:
				// on view
				$controller->onview();

				// make controller data global
				foreach ($controller as $_var => $_val) {
					$$_var = $_val;
				}

				unset($_var);
				unset($_val);

				// and load view file
				Page::open();
				require template('seeing');

				// load the raw view file
				echo file_get_contents(self::$configuration->state->view);

				// check build
				require template('saw');
				Page::close();

				// load the parsed build file
				require self::$configuration->state->build;
				break;

			case Router::METHOD:
				// on method
				$controller->onmethod();
				$response = new Response(Response::IN_PROCESS);
				$method = Router::req(Router::$uri->method);
				$arguments = Router::req(Router::$uri->args);

				if (!$arguments) {
					$arguments = array();
				}

				// check if method exits
				if (!method_exists($controller, $method)) {
					$response->status = Response::METHOD_UNKNOWN_METHOD;
					die($response);
				}

				// check if method is public
				if (!in_array($method, $controller->public_methods)) {
					$response->status = Response::METHOD_PRIVATE_METHOD;
					die($response);
				}

				// call the method
				$response->response = call_user_func_array(
					array($controller, $method),
					$arguments
				);

				die($response);
		}
	}
}
