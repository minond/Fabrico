<?php

class Fabrico {
	// states
	const ERROR = 'error';
	const SUCCESS = 'success';
	const UNKNOWN_FILE = 'unknown file';

	// resource file checks
	const PATH_ABSOLUTE = '/http|^\//';
	const PATH_INTERNAL = '/\^\//';
	const PATH_INTERNAL_STR = '^/';

	// project/page information
	public static $file;
	public static $controller;
	private static $req;
	private static $method;
	private static $action;
	private static $config;
	private static $debugging;

	// pre request information
	private static $uri_query_file = '_file';
	private static $uri_query_arg = '_arg';
	private static $uri_query_env = '_env';
	private static $uri_query_method = '_method';
	private static $uri_query_action = '_action';
	private static $uri_query_debug = '_debug';

	// default controller information
	private static $file_config = '../config/config.ini';
	private static $file_debug = 'debug.log';
	private static $file_project = 'config/config.ini';
	private static $def_debugging = 'FabricoDebugging';
	private static $def_controller = 'Fabrico.controller.php';
	private static $def_controller_name = 'FabricoController';
	public static $tpl_helper = 'Fabrico.template.php';

	/**
	 * @name redirect
	 * @var stdClass
	 * bad request handers
	 */
	public static $redirect;

	/**
	 * @name directory
	 * @var stdClass
	 * standard project directory structure
	 */
	public static $directory;
	
	/**
	 * @name service
	 * @var stdClass
	 * service loader/manager scripts
	 */
	public static $service;

	/**
	 * @name init
	 * @param array request object
	 * initializes project and requested file settings
	 */
	public static function init (& $req) {
		self::$req =& $req;

		self::$config = new stdClass;
		self::$redirect = new stdClass;
		self::$redirect->_404_redirect = '';
		self::$redirect->_404_header = 'HTTP/1.0 404 Not Found';

		self::$file = self::$req[ self::$uri_query_file ];
		$settings = (object) parse_ini_file(self::$file_config, true);

		foreach ($settings as $section => $setting) {
			$settings->{ $section } = (object) $setting;
		}

		self::$directory = $settings->directory;
		self::$service = $settings->service;
		self::$config->loading = $settings->loading;
		self::$config->internal = $settings->internal;
		
		self::$config->project = (object) parse_ini_file(
			$settings->loading->prefix .
			$settings->loading->path .
			self::$file_project, true
		);

		foreach (self::$config->project as $section => $setting) {
			self::$config->project->{ $section } = (object) $setting;
		}

		return file_exists(self::get_requested_file());
	}

	/**
	 * @name check_debugging
	 * checks if requeste and project are in debug mode
	 */
	public static function check_debugging () {
		self::$debugging = false;

		if (isset(self::$req[ self::$uri_query_debug ])) {
			setcookie(self::$def_debugging, self::$req[ self::$uri_query_debug ]);
			$_COOKIE[ self::$def_debugging ] = self::$req[ self::$uri_query_debug ];
		}

		if (isset($_COOKIE[ self::$def_debugging ])) {
			self::$debugging = $_COOKIE[ self::$def_debugging ] === '1';
		}
	}

	/**
	 * @name clean_file
	 * @return string clean/valid file name
	 */
	public static function clean_file ($file) {
		return preg_replace(
			array('/\/$/', '/\s/', '/-/', '/\..+$/'), 
			array('', '_', '_', ''), 
			$file
		);
	}

	/**
	 * @name file_path
	 * @param string file name
	 * @param bool optional file path starts at root
	 * @param bool optional file is a framework file
	 * @return string path to project file
	 */
	public static function file_path ($file, $root = false, $int = false) {
		$path = $root ? self::$config->loading->root : self::$config->loading->prefix;
		$path = $int ? self::$config->loading->internal : $path;

		if ($int) {
			$file = preg_replace(self::PATH_INTERNAL, '', $file);
		}
		else {
			$path .= self::$config->loading->path;
		}

		return $path . $file;
	}

	/**
	 * @name get_config
	 * @return stdClass configuration object
	 */
	public static function get_config () {
		return self::$config;
	}

	/**
	 * @name get_log_file
	 * @return string log file path
	 */
	public static function get_log_file () {
		return self::file_path(
			self::$directory->logs .
			self::$file_debug
		);
	}

	/**
	 * @name get_controller_file
	 * @param string optional standard controller name
	 * @param bool optional no file checks
	 * @return string controller file path
	 */
	public static function get_controller_file ($file = '', $def = false) {
		if (!$file) {
			$file = self::$file;
		}

		$cfile = self::file_path(
			self::$directory->controllers .
			self::clean_file($file) .
			self::$config->loading->suffix
		);

		if (!$def && !file_exists($cfile)) {
			$cfile = self::$def_controller;
			self::$controller = self::$def_controller_name;
		}
		else {
			self::$controller = ucwords(self::clean_file($file));
		}

		return $cfile;
	}

	/**
	 * @name get_requested_file
	 * @return string requested file path
	 */
	public static function get_requested_file () {
		return self::file_path(
		       self::$directory->views .
			   self::clean_file(self::$file) . 
			   self::$config->loading->suffix
		);
	}

	/**
	 * @name get_action_file
	 * @param string action file name
	 * @return string action file path
	 */
	public static function get_action_file ($file) {
		return self::file_path(
			self::$directory->actions .
			self::clean_file($file) .
			self::$config->loading->suffix
		);
	}

	/**
	 * @name get_main_view_pre_file
	 * @return string view file loaded before pre
	 */
	public static function get_main_view_pre_file () {
		return self::file_path(
		       self::$directory->internals .
		       self::$directory->views .
			   self::$config->internal->seeing .
			   self::$config->loading->suffix
		);
	}

	/**
	 * @name get_main_view_post_file
	 * @return string view file loaded before pre
	 */
	public static function get_main_view_post_file () {
		return self::file_path(
		       self::$directory->internals .
		       self::$directory->views .
			   self::$config->internal->saw .
			   self::$config->loading->suffix
		);
	}

	/**
	 * @name get_requested_file
	 * @param string file url
	 * @param string file extension
	 * @return string file path
	 */
	public static function get_resource_file ($url, $extension) {
		// external resource check
		if (preg_match(self::PATH_ABSOLUTE, $url)) {
			return $url;
		}

		switch ($extension) {
			case Resource::EXT_JS:
				$dir = self::$directory->javascript;
				break;

			case Resource::EXT_CSS:
				$dir = self::$directory->css;
				break;

			default:
				$dir = self::$directory->resource;
				break;
		}

		return self::file_path(
		       $dir . $url, true, preg_match(self::PATH_INTERNAL, $url)
		);
	}

	/**
	 * @name redirect
	 * @return redirects user after bad request
	 */
	public static function redirect () {
		strlen(self::$redirect->_404_redirect) ? 
			require self::$redirect->_404_redirect :
			header(self::$redirect->_404_header);
	}

	/**
	 * @name get_request_call_info
	 * @return array controller, arguments, enviroment
	 */
	private static function get_request_call_info () {
		$arg = array();
		$env = array();

		// load controller
		require_once self::get_controller_file();
		$controller = new self::$controller;

		// argument check
		if (isset(self::$req[ self::$uri_query_arg ])) {
			$arg = self::$req[ self::$uri_query_arg ];
		}

		// enriroment check
		if (isset(self::$req[ self::$uri_query_env ])) {
			$env = self::$req[ self::$uri_query_env ];
		}

		return array(& $controller, & $arg, & $env);
	}

	/**
	 * @name get_request_call_response
	 * @param mixed variable to be formatted
	 * @return mixed formatted variable
	 */
	private static function get_request_call_response (& $ret) {
		if (is_array($ret) || is_object($ret)) {
			$ret = json_encode($ret);
		}

		return $ret;
	}

	/**
	 * @name init_template
	 * loads and initializes the controller and view file
	 * controller is created under local scope, but is available
	 * within the template file
	 */
	public static function init_template () {
		// load and initialize the controller
		require_once self::get_controller_file();
		$controller = new Fabrico::$controller;

		// setup enviroment
		foreach ($controller as $key => $value) {
			global $$key;
			$$key = $value;
		}

		// load the view
		require self::$tpl_helper;
		require self::get_main_view_pre_file();
		require self::get_requested_file();
		require self::get_main_view_post_file();

		// and clear from memory
		unset($controller);
	}

	/**
	 * @name init_method
	 * loads the current page's controller and calls it's requested method.
	 */
	public static function init_method () {
		$ret = new stdClass;
		self::$method = self::$req[ self::$uri_query_method ];
		list($controller, $arg, $env) = self::get_request_call_info();

		// method check
		if (method_exists($controller, self::$method) && $controller->registered(self::$method)) {
			// set up enviroment
			foreach ($env as $key => $value) {
				if (property_exists($controller, $key)) {
					$controller->{ $key } = $value;
				}
			}

			// and call method
			$ret->status = self::SUCCESS;
			$ret->msg = call_user_func_array(array($controller, self::$method), $arg);
		}
		else {
			$ret->status = self::ERROR;
			$ret->msg = self::UNKNOWN_FILE;
		}
		
		die(self::get_request_call_response($ret));
	}

	/**
	 * @name init_action
	 * checks if current page is allowed to take a certain action and runs it
	 */
	public static function init_action () {
		self::$action = self::$req[ self::$uri_query_action ];

		if (!file_exists(self::get_action_file(self::$action))) {
			die;
		}

		list($controller, $arg, $env) = self::get_request_call_info();
		
		// check action
		if ($controller->allows(self::$action)) {
			// set up enviroment
			foreach ($env as $key => $value) {
				global $$key;
				$$key = $value;
			}

			// load action
			require_once self::get_action_file(self::$action);

			// and call it
			$ret->status = self::SUCCESS;
			$ret->msg = call_user_func_array(self::$action, $arg);
		}
		else {
			$ret->status = self::ERROR;
			$ret->msg = self::UNKNOWN_FILE;
		}

		die(self::get_request_call_response($ret));
	}

	/**
	 * @name is_internal
	 * @return bool true if requested file is an internal script
	 */
	public static function is_internal () {
		return in_array(self::clean_file(self::$file), self::$config->internal->files);
	}

	/**
	 * @name is_view_request
	 * @return bool true if requested file is a view page
	 */
	public static function is_view_request () {
		return isset(self::$req[ self::$uri_query_file ]) && 
			   strlen(self::$req[ self::$uri_query_file ]) &&
		       !self::is_method_request() &&
		       !self::is_action_request();
	}

	/**
	 * @name is_method_request
	 * @return bool true if request is a method request
	 */
	public static function is_method_request () {
		return isset(self::$req[ self::$uri_query_file ]) && 
		       isset(self::$req[ self::$uri_query_method ]) &&
			   strlen(self::$req[ self::$uri_query_method ]) &&
			   !self::is_action_request();
	}

	/**
	 * @name is_action_request
	 * @return bool true if request is an action request
	 */
	public static function is_action_request () {
		return isset(self::$req[ self::$uri_query_file ]) && 
		       isset(self::$req[ self::$uri_query_action ]) &&
			   strlen(self::$req[ self::$uri_query_action ]) &&
			   !self::is_method_request();
	}

	/**
	 * @name is_debugging
	 * @return bool debugging state
	 */
	public static function is_debugging () {
		return self::$debugging;
	}
}
