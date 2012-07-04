<?php

class mFrame {
	private static $file;
	private static $config;

	public static $can_load = false;
	private static $uri_query_file = '_file';
	private static $config_file = '../config/config.ini';

	// request handlers
	public static $req_redirect_404 = '';
	public static $req_required_404 = '';

	/**
	 * @name init
	 * initializes project and requested file settings
	 */
	public static function init () {
		global $program;

		self::$file = $_REQUEST[ self::$uri_query_file ];
		self::$config = (object) parse_ini_file(self::$config_file, true);

		foreach (self::$config as $section => $settings) {
			self::$config->{ $section } = (object) $settings;
		}
		
		self::$can_load = file_exists(self::get_file_requested());
		$program->controller = self::get_controller_file(self::$file);

		return self::$can_load;
	}

	public static function get_controller_file ($file) {
		global $program, $directory;

		return self::$config->loading->prefix .
		       self::$config->project->path .
		       $directory->controllers .
		       self::get_clean_file($file) .
		       self::$config->loading->suffix;
	}

	/**
	 * @name get_config
	 * @return stdClass configuration object
	 */
	public static function get_config () {
		return self::$config;
	}

	/**
	 * @name get_file_requested
	 * @return string requested file path
	 */
	public static function get_file_requested () {
		return self::get_file_path(
			   self::get_clean_file(self::$file) . 
			   self::$config->loading->suffix
		);
	}

	/**
	 * @name get_clean_file
	 * @return string clean/valid file name
	 */
	public static function get_clean_file ($file) {
		return preg_replace(
			array('/\/$/', '/\s/', '/-/', '/\..+$/'), 
			array('', '_', '_', ''), 
			$file
		);
	}

	/**
	 * @name get_file_path
	 * @return string path to project file
	 */
	public static function get_file_path ($file) {
		return self::$config->loading->prefix . self::$config->project->path .$file;
	}

	/**
	 * @name redirect
	 * @return redirects user after bad request
	 */
	public static function redirect () {
		if (strlen(self::$req_redirect_404)) {
			require self::$req_redirect_404;
		}
		else {
			header('HTTP/1.0 404 Not Found');
		}
	}
}
