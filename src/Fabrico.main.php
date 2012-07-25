<?php

class Fabrico {
	// statuses
	const ERROR = 'error';
	const SUCCESS = 'success';
	const IN_PROCESS = 'in_process';
	const NOT_ALLOWED = 'not_allowed';
	const UNKNOWN_FILE = 'unknown_file';
	const UNKNOWN_ACTION = 'unknown_action';
	const UNKNOWN_METHOD = 'unknown_method';

	// log file types
	const FILE_LOG = 'debug.log';
	const FILE_QUERY = 'query.log';
	const FILE_REQUEST = 'request.log';
	const FILE_ERROR = 'error.log';


	// resource file checks
	const PATH_ABSOLUTE = '/http|^\//';
	const PATH_INTERNAL = '/\^\//';
	const PATH_INTERNAL_STR = '^/';

	// action names
	const ACTION_FORMAT = '_%s_';
	const METHOD_GETTER = 'get_%s';

	// project/page information
	public static $file;
	public static $controller;
	public static $control;
	public static $req;
	private static $id;
	private static $method;
	private static $action;
	private static $config;
	private static $debugging;
	private static $validview;
	private static $time_start;
	private static $time_end;
	private static $time_total;
	private static $start_mem;

	// pre request information
	public static $uri_query_error = '_error';
	public static $uri_query_file = '_file';
	public static $uri_query_arg = '_args';
	public static $uri_query_env = '_env';
	public static $uri_query_method = '_method';
	public static $uri_query_action = '_action';
	public static $uri_query_debug = '_debug';
	public static $uri_query_success = '_success';
	public static $uri_query_fail = '_fail';
	public static $uri_query_invalid = '_ivd';
	public static $uri_query_id = 'id';

	// default controller information
	public static $file_config = '../config/config.ini';
	private static $file_project = '/config/config.ini';
	private static $def_controller = 'Fabrico.controller.php';
	private static $def_controller_name = 'MainController';
	private static $def_controller_suffix = 'Controller';
	private static $def_debugging = 'FabricoDebugging';

	// misc
	const STR_VIEW = 'view';
	const STR_METHOD = 'method';
	const STR_ACTION = 'action';
	const STR_UNKNOWN = 'unknown';
	const STR_REQUEST = 'request';
	const STR_ERROR = 'error';

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
	 * @name initialize
	 * @param array request object
	 * initializes project and requested file settings
	 */
	public static function initialize (& $req) {
		self::$req =& $req;
		self::$id = substr((string) rand(), 0, 5);
		self::$config = new stdClass;
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

		// load the routing
		FabricoURL::project($settings);
		$rules = self::file_path(
			self::$directory->routing .
			self::$config->internal->router .
			self::$config->loading->suffix
		);

		if (file_exists($rules)) {
			require_once $rules;
			FabricoURL::run();
		}

		self::$file = self::$req[ self::$uri_query_file ];
		self::$validview = file_exists(self::get_requested_file());
		self::check_debugging();
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
			self::$debugging = $_COOKIE[ self::$def_debugging ] === '1';
		}
		else if (isset($_COOKIE[ self::$def_debugging ])) {
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
	 * @name clean_action_name
	 * @param string action name
	 * @return string fabrico action name
	 */
	public static function clean_action_name ($action) {
		return sprintf(self::ACTION_FORMAT, $action);
	}

	/**
	 * @name clean_getter_name
	 * @param string method name
	 * @return string gettter method name
	 */
	public static function clean_getter_name ($method) {
		return sprintf(self::METHOD_GETTER, $method);
	}

	/**
	 * @name core_file_path
	 * @param string file name
	 * @return string path to internal fabrico file
	 */
	public static function core_file_path ($file) {
		return self::$config->loading->core . $file;
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
	public static function get_log_file ($type = self::FILE_LOG) {
		return self::file_path(self::$directory->logs . $type);
	}

	/**
	 * @name get_id
	 * @return int guid
	 */
	public static function get_id () {
		return self::$id;
	}

	/**
	 * parses a view file request and returns a list of possible
	 * locations and file names for the controller file, in order
	 * of most relevance.
	 *
	 * @name get_possible_controller_files
	 * @param string requested file path
	 * @return array of possible controller names and paths
	 */
	public static function get_possible_controller_files ($filepath) {
		$paths = explode('/', $filepath);
		$possible = array();
		$files = array();
		$dirs = array();

		for ($i = 0, $max = count($paths); $i < $max; $i++) {
			for ($j = $i; $j < $max; $j++) {
				if (!isset($files[ $j ])) {
					$files[ $j ] = '';
					$dirs[ $j ] = '';
				}

				$files[ $j ] .= $paths[ $i ];
				$dirs[ $j ] .= $paths[ $i ] . '/';
			}
		}

		foreach ($files as $index => $file) {
			$possible[] = $file;
			$possible[] = rtrim($dirs[ $index ], '/');
		}

		array_shift($possible);
		return array_reverse(array_unique($possible));
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

		$possible = self::get_possible_controller_files($file);

		foreach ($possible as $possibility) {
			$cfile = self::file_path(
				self::$directory->controllers .
				self::clean_file($possibility) .
				self::$config->loading->suffix
			);

			if (file_exists($cfile)) {
				$file = $possibility;
				break;
			}
		}

		if (!$def && !file_exists($cfile)) {
			$cfile = self::$def_controller;
			self::$controller = self::$def_controller_name;
		}
		else {
			self::$controller = ucwords(self::clean_file($file)) . self::$def_controller_suffix;
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
	 * @name get_element_file
	 * @param string element file name
	 * @return string element file path
	 */
	public static function get_element_file ($elem) {
		$custom = self::file_path(
			self::$directory->elements .
			self::clean_file($elem) .
			self::$config->loading->suffix
		);

		$internal = self::core_file_path(
			self::$directory->elements .
			self::clean_file($elem) .
			self::$config->loading->suffix
		);

		return file_exists($custom) ? $custom : $internal;
	}

	/**
	 * @name get_model_file
	 * @param string medel name
	 * @return string model file path
	 */
	public static function get_model_file ($model) {
		return self::file_path(
			self::$directory->models .
			self::clean_file($model) .
			self::$config->loading->suffix
		);
	}

	/**
	 * @name get_main_controller_file
	 * @return string main controller file path
	 */
	public static function get_main_controller_file () {
		return self::file_path(
		       self::$directory->internals .
		       self::$directory->controllers .
		       self::$config->internal->controller .
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
	 * @name get_template_file
	 * @param string template name
	 * @return string template file path
	 */
	public static function get_template_file ($template) {
		$custom = self::file_path(
			self::$directory->templates .
			self::clean_file($template) .
			self::$config->loading->suffix
		);

		$internal = self::core_file_path(
			self::$directory->templates .
			self::clean_file($template) .
			self::$config->loading->suffix
		);

		return file_exists($custom) ? $custom : $internal;
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
			case FabricoPageResource::EXT_JS:
				$dir = self::$directory->javascript;
				break;

			case FabricoPageResource::EXT_CSS:
				$dir = self::$directory->css;
				break;

			case FabricoPageResource::EXT_IMG:
				$dir = self::$directory->image;
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
	public static function redirect ($type = 404) {
		header('HTTP/1.0 404 Not Found');
		$file = self::file_path(
			self::$directory->redirect .
			$type .
			self::$config->loading->suffix
		);

		if (file_exists($file)) {
			// load and initialize the controller
			require_once self::get_main_controller_file();
			require_once self::get_controller_file();
			self::$control = new self::$controller;
			$control =& self::$control;

			// call onview method
			$control->onview();

			// setup enviroment
			foreach (self::$control as $key => $value) {
				$$key = $value;
			}

			require self::get_main_view_pre_file();
			include $file;
			require self::get_main_view_post_file();
		}
	}

	/**
	 * @name get_request_call_info
	 * @return array controller, arguments, enviroment
	 */
	private static function get_request_call_info () {
		$arg = array();
		$env = array();

		// load controller
		require_once self::get_main_controller_file();
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
		require_once self::get_main_controller_file();
		require_once self::get_controller_file();
		self::$control = new self::$controller;
		$control =& self::$control;

		// call onview method
		$control->onview();

		// setup enviroment
		foreach (self::$control as $key => $value) {
			$$key = $value;
		}

		// load the view
		require self::get_main_view_pre_file();
		require self::get_requested_file();
		require self::get_main_view_post_file();
	}

	/**
	 * @name init_method
	 * loads the current page's controller and calls it's requested method.
	 */
	public static function init_method () {
		$ret = new FabricoResponse(self::IN_PROCESS);
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
			
			// call onmethod
			$controller->onmethod();

			// and call method
			$ret = new FabricoResponse(
				self::SUCCESS, 
				call_user_func_array(array($controller, self::$method), $arg)
			);
		}
		else {
			$err = FabricoResponse::error(self::NOT_ALLOWED, self::$method, __FILE__, __LINE__);
			$ret = new FabricoResponse(self::ERROR, $err);
		}
		
		$ret->out();
	}

	/**
	 * @name init_action
	 * checks if current page is allowed to take a certain action and runs it
	 */
	public static function init_action () {
		self::$action = self::$req[ self::$uri_query_action ];
		$action = self::clean_action_name(self::$action);
		$ret = new FabricoResponse(self::IN_PROCESS);

		if (!file_exists(self::get_action_file(self::$action))) {
			$err = FabricoResponse::error(self::UNKNOWN_FILE, self::get_action_file(self::$action), __FILE__, __LINE__);
			$ret = new FabricoResponse(self::ERROR, $err);
		}
		else {
			list($controller, $arg, $env) = self::get_request_call_info();
		
			// check action
			if ($controller->allows(self::$action)) {
				// set up enviroment
				foreach ($env as $key => $value) {
					$$key = $value;
				}

				// call onaction method
				$controller->onaction();

				// load action
				require_once self::get_action_file(self::$action);

				// and call it
				if (function_exists($action)) {
					$ret = new FabricoResponse(
						self::SUCCESS, 
						call_user_func_array($action, $arg)
					);
				}
				else {
					$err = FabricoResponse::error(self::UNKNOWN_ACTION, $action, __FILE__, __LINE__);
					$ret = new FabricoResponse(self::ERROR, $err);
				}
			}
			else {
				$err = FabricoResponse::error(self::NOT_ALLOWED, self::$action, __FILE__, __LINE__);
				$ret = new FabricoResponse(self::ERROR, $err);
			}
		}

		$ret->out();
	}

	/**
	 * @name is_error_request
	 * @return bool true if request of to an error page
	 */
	public static function is_error_request () {
		return isset(self::$req[ self::$uri_query_error ]);
	}

	/**
	 * @name is_post
	 * @return boolean true if current request is using the POST method
	 */
	public static function is_post () {
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	/**
	 * @name is_valid_view
	 * @return bool true if requested file an a view file
	 */
	public static function is_valid_view () {
		return self::$validview;
	}

	/**
	 * @name is_view_request
	 * @return bool true if requested file is a view page
	 */
	public static function is_view_request () {
		return isset(self::$req[ self::$uri_query_file ]) && 
		       strlen(self::$req[ self::$uri_query_file ]) &&
		       self::is_valid_view() &&
		       !self::is_error_request() &&
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
		       self::is_post() &&
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
		       self::is_post() &&
		       !self::is_method_request();
	}

	/**
	 * @name is_debugging
	 * @return bool debugging state
	 */
	public static function is_debugging () {
		return self::$debugging;
	}

	/**
	 * @name timer_start
	 */
	public static function timer_start () {
		if (!isset(self::$time_start)) {
			self::$start_mem = memory_get_usage();
			self::$time_start = microtime();
		}
	}

	/**
	 * @name timer_stop
	 */
	public static function timer_stop () {
		self::$time_end = microtime();
		self::$time_total = self::$time_end - self::$time_start;
	}

	/**
	 * @name timer_log
	 */
	public static function timer_log () {
		$uri = $_SERVER['REQUEST_URI'];

		switch (true) {
			case self::is_view_request():
				$type = self::STR_VIEW;
				break;

			case self::is_method_request():
				$type = self::STR_METHOD;
				break;

			case self::is_action_request():
				$type = self::STR_ACTION;
				break;

			case self::is_error_request():
				$type = self::STR_ERROR;
				$uri = substr($uri, strpos($uri, '=') + 1);
				break;

			default:
				$type = self::STR_UNKNOWN;
				break;
		}

		util::loglist(self::STR_REQUEST, array(
			'type' => $type,
			'uri'=> $uri,
			'file' => self::get_requested_file(self::$file),
			'cont' => self::$controller,
			'time' => self::$time_total,
			'end' => memory_get_usage() . ' bytes',
			'start' => self::$start_mem . ' bytes'
		), self::FILE_REQUEST);
	}

	/**
	 * @name req
	 * @param string query parameter
	 * @return string parameter value
	 */
	public static function req ($key) {
		return isset(self::$req[ $key ]) ? self::$req[ $key ] : '';
	}

	/**
	 * @name ses
	 * @param string session parameter
	 * @param mixed session value
	 */
	public static function ses ($name) {
		return isset($_SESSION[ $name ]) ? $_SESSION[ $name ] : '';
	}

	/**
	 * @name set_cookie
	 * @param string cookie name
	 * @param string cookie value
	 */
	public static function set_cookie ($name, $value) {
		setcookie($name, $value);
		$_COOKIE[ $name ] = $value;
	}

	/**
	 * @name static get_cookie
	 * @param string cookie name
	 * @return string cookie value
	 */
	public static function get_cookie ($name) {
		return $_COOKIE[ $name ];
	}

	/**
	 * @name array2query
	 * @param array of key value pairs
	 * @param boolean include question mark
	 * @return string query string
	 */
	public static function array2query (& $list, $noq = false) {
		$items = array();

		foreach ($list as $key => $value)
			$items[] = $key . '=' . $value;

		$items = implode('&', $items);
		return $items ? (
			$noq ? $items : '?' . $items
		) : '';
	}

	/**
	 * @name handle_request
	 * @param boolean profile request time
	 */
	public static function handle_request ($profile = false) {
		if ($profile) {
			self::timer_start();
		}

		// regular page request
		if (self::is_view_request()) {
			self::init_template();
		}
		// controller method call request
		else if (self::is_method_request()) {
			self::init_method();
		}
		// action method call request
		else if (self::is_action_request()) {
			self::init_action();
		}
		// error redirect request
		else if (self::is_error_request()) {
			self::redirect(500);
		}
		// unknown
		else {
			self::redirect();
		}

		if ($profile) {
			self::timer_stop();
			self::timer_log();
		}
	}

	/**
	 * @name handle_success
	 * @param array redirect arguments
	 */
	public static function handle_success ($args = array()) {
		if (self::is_view_request()) {
			return false;
		}

		$query = false;
		$redirect = isset(self::$req[ self::$uri_query_success ]) ?
		            self::$req[ self::$uri_query_success ] : self::$file;

		if (count($args) === 1) {
			foreach ($args as $arg => $value) {
				if ($arg === self::$uri_query_id) {
					$query = "/{$value}";
				}
			}
		}

		if (!$query) {
			$query = self::array2query($args);
		}

		header('Location: ' . $redirect . $query);
	}

	/**
	 * @name handle_failure
	 * @param array redirect arguments
	 */
	public static function handle_failure ($args = array()) {
		if (self::is_view_request()) {
			return false;
		}

		$redirect = isset(self::$req[ self::$uri_query_fail ]) ?
		            self::$req[ self::$uri_query_fail ] : self::$file;

		header('Location: ' . $redirect . self::array2query($args));
	}

	/**
	 * @name is_invalid
	 * @param string thing
	 * @return boolean invalid thing
	 */
	public static function is_invalid ($thing) {
		$list = self::req( self::$uri_query_invalid );

		return $list ? in_array($thing, $list) : false;
	}
}
