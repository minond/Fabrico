<?php

namespace Fabrico;

class Core {
	/**
	 * core dependancies
	 * @var array
	 */
	public static $deps = [
		'../deps/sfYaml/sfYaml.php',
		'../deps/ActiveRecord/ActiveRecord.php',
		'../deps/Dom/Dom.php'
	];

	/**
	 * helper files
	 * @var array
	 */
	public static $help = [
		'../help/fabrico.format.php',
		'../help/fabrico.controllers.php',
		'../help/fabrico.authentication.php',
		'../help/fabrico.views.php',
		'../help/fabrico.merge.php',
		'../help/fabrico.test.php',
		'../help/fabrico.page.php',
		'../help/fabrico.pagination.php',
		'../help/fabrico.utils.php',
		'../help/fabrico.params.php',
		'../help/fabrico.std.php'
	];

	/**
	 * core files
	 * @var array
	 */
	public static $core = [
		'fabrico.utils.php',
		'fabrico.log.php',
		'fabrico.router.php',
		'fabrico.project.php',
		'fabrico.controller.php',
		'fabrico.page.php',
		'fabrico.tag.php',
		'fabrico.response.php',
		'fabrico.build.php',
		'fabrico.element.php',
		'fabrico.template.php',
		'fabrico.error.php',
		'fabrico.arbol.php',
		'fabrico.state.php',
		'fabrico.dataset.php',
		'fabrico.view.php'
	];

	/**
	 * controller reference
	 * @var Controller
	 */
	public static $controller;

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
		self::$configuration->configuration = (object) self::$configuration->configuration;
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
	 * loads core dependancies and modules
	 */
	public static function load_core_files () {
		$files = array_merge([], self::$deps, self::$help, self::$core);

		foreach ($files as $file) {
			require_once $file;
		}
	}

	/**
	 * handles current request loading views and controllers
	 */
	public static function handle_request () {
		// get controller information
		$controller_info = self::$configuration->state->controller;
		$start = microtime();

		Logger::request('uri: ' . self::$configuration->state->uri);
		Logger::request('view: ' . self::$configuration->state->uri);
		Logger::request('controller: ' . $controller_info->controller_name);
		Logger::request('type: ' . Router::request_method());

		// load standard controller
		require_once Project::get_controller_file(
			self::$configuration->convention->controller_default_file
		);

		// load controller
		if ($controller_info->file_path) {
			require_once $controller_info->file_path;
		}

		// and instanciate it and load state
		self::$controller = new $controller_info->controller_real_name;
		State::load(self::$controller);
		self::$controller->setup();

		// and send it to the router
		Router::handle_request(self::$controller, self::$configuration->state->build, true);

		// clean up
		State::save(self::$controller);
		Logger::request('time: ' . (microtime() - $start));
	}

	/**
	 * loads project specific configuration
	 */
	public static function load_project_configuration () {
		// custom routing
		self::$configuration->routing = (object) \sfYaml::load(
			Project::get_configuration_file(
				Core::$configuration->configuration->routing
			)
		);

		// database configuration
		self::$configuration->database = (object) \sfYaml::load(
			Project::get_configuration_file(
				Core::$configuration->configuration->database
			)
		);

		// load db authentication file
		$dbauth = (object) \sfYaml::load(
			self::$configuration->database->credentials
		);

		// and save them
		self::$configuration->database->username = $dbauth->username;
		self::$configuration->database->password = $dbauth->password;
	}

	/**
	 * initializes the session
	 */
	public static function start_session () {
		session_start();
	}

	/**
	 * initializes Active Record
	 */
	public static function start_active_record () {
		\ActiveRecord\Config::initialize(function ($cg) {
			$db = Core::$configuration->database;

			$cg->set_model_directory(Project::get_model_directory());
			$cg->set_connections([
				'development' => "{$db->type}://{$db->username}:{$db->password}@{$db->host}/" .
				                 $db->databases[ $db->active ]
			]);
		});
	}

	/**
	 * kills process on non-page request
	 */
	public static function request_pre_check () {
		if (in_array($_SERVER['REQUEST_URI'], [ '/favicon.ico' ])) {
			die;
		}
	}
}