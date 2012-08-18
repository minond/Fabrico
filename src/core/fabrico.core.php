<?php

namespace Fabrico;

class Core {
	/**
	 * core dependancies
	 *
	 * @var array
	 */
	public static $deps = array(
		'../deps/sfYaml/sfYaml.php',
		'../deps/ActiveRecord/ActiveRecord.php',
		'../deps/Dom/Dom.php'
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
		$is_404 = false;

		// load controller
		if ($controller_info->file_path) {
			require_once $controller_info->file_path;
		}

		// and instanciate it
		$controller = "\\{$controller_info->controller_name}";
		$controller = new $controller;
		$view_method = Router::request_method();

		Logger::request('uri: ' . self::$configuration->state->uri);
		Logger::request('view: ' . self::$configuration->state->uri);
		Logger::request('controller: ' . $controller_info->controller_name);
		Logger::request('type: ' . $view_method);

		switch ($view_method) {
			case Router::R404:
				$is_404 = true;
			case Router::VIEW:
			case Router::JSON:
				// make controller data global
				foreach ($controller as $_var => $_val) {
					$$_var = $_val;
				}

				unset($_var);
				unset($_val);

				switch ($view_method) {
					// load the raw view file and check build
					case Router::VIEW:
						// on view
						$controller->onview();

						// build view
						Page::build();
						require self::$configuration->state->build;
						echo Page::close();
						break;

					case Router::R404:
						$data_response = false;
						
						if (method_exists($controller, 'ondata')) {
							// on data
							$data_response = $controller->ondata();
							$is_404 = false;
						}
						else {
							$is_404 = true;
						}

						if (is_array($data_response) || is_object($data_response)) {
							switch (Router::data_method()) {
								case Router::JS:
								case Router::JSON:
									Router::type_header(Router::JSON);
									$data = json_encode($data_response);

									if (Router::data_method() === Router::JS) {
										$data = $_REQUEST['cb'] . "({$data})";
									}

									echo $data;

									break;

								case Router::XML:
									Router::type_header(Router::XML);
									echo \DOM::arrayToXMLString($data_response, 'root', true);
									break;

								case Router::CSV:
									Router::type_header(Router::CSV);
									echo \DOM::arrayToCSVString($data_response, ', ');
									break;

								default:
									$is_404 = true;
									break;
							}
						}
						
				}

				break;

			case Router::METHOD:
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
				if (!in_array($method, $controller->public)) {
					$response->status = Response::METHOD_PRIVATE_METHOD;
					die($response);
				}

				// on method
				$controller->onmethod();

				// call the method
				$response->status = Response::SUCCESS;
				$response->response = call_user_func_array(
					array($controller, $method),
					$arguments
				);

				die($response);
				break;

			default:
				$is_404 = true;
				break;
		}

		if ($is_404) {
			// display
			Router::http_header(Router::R404);
			require template('redirect/404');
		}
	}

	/**
	 * loads project specific configuration
	 */
	public static function load_project_configuration () {
		self::$configuration->database = (object) \sfYaml::load(
			Project::get_configuration_file('database')
		);
	}

	/**
	 * initializes Active Record
	 */
	public static function start_active_record () {
		\ActiveRecord\Config::initialize(function ($cg) {
			$db = Core::$configuration->database;

			$cg->set_model_directory(Project::get_model_directory());
			$cg->set_connections(array(
				'development' => "{$db->type}://{$db->username}:{$db->password}@{$db->host}/" .
				                 $db->databases[ $db->active ]
			));
		});
	}
}
