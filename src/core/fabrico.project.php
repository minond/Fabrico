<?php

namespace Fabrico;

class Project {
	/**
	 * requested file
	 *
	 * @var string
	 */
	public static $file;

	/**
	 * loads the view and controller files into the router
	 */
	public static function set_files () {
		self::$file = Router::get_file_requested(true);

		Core::$configuration->state->uri = self::$file;
		Core::$configuration->state->view = self::get_view_file();
		Core::$configuration->state->controller = self::get_controller_file();
	}

	/**
	 * returns the path to a project file
	 *
	 * @param string file name
	 * @param boolean use default file extension
	 * @return string file path
	 */
	private static function get_project_file ($name, $defext = false) {
		return self::get_project_directory() . $name .
		       ($defext ? Core::$configuration->loading->suffix : '');
	}

	/**
	 * returns the path to a project directory
	 *
	 * @return string directory path
	 */
	private static function get_project_directory () {
		return Core::$configuration->loading->prefix .
		       Core::$configuration->project;
	}

	/**
	 * finds the view file for the current request
	 *
	 * @return string
	 */
	public static function get_view_file () {
		return self::get_project_file(
			Core::$configuration->directory->views .
			self::$file, true
		);
	}

	/**
	 * find the controller file for the current request
	 *
	 * @return object with controller path and name
	 */
	public static function get_controller_file () {
		$parts = explode('/', self::$file);
		$possibilities = array();

		$controller = new \stdClass;
		$controller->file_path = null;
		$controller->controller_name = Core::$configuration->convention->controller_default;

		// possible file paths
		for ($i = 0, $len = count($parts); $i < $len; $i++) {
			for ($j = $i; $j < $len; $j++) {
				if (!isset($possibilities[ $j ])) {
					$possibilities[ $j ] = '';
				}

				$possibilities[ $j ] .= $i ? DIRECTORY_SEPARATOR : '';
				$possibilities[ $j ] .= $parts[ $i ];
			}
		}

		// possible controller names
		foreach ($possibilities as $index => $path) {
			$possibilities[ $index ] = clone $controller;
			$possibilities[ $index ]->controller_name = ucwords($parts[ $index ]) .
			                                 Core::$configuration->convention->controller_suffix;
			$possibilities[ $index ]->file_path = self::get_project_file(
				Core::$configuration->directory->controllers . $path, true
			);
		}

		// real controller information
		for ($i = count($possibilities) - 1; $i >= 0; $i--) {
			if (file_exists($possibilities[ $i ]->file_path)) {
				$controller = $possibilities[ $i ];
				break;
			}
		}
		
		return $controller;
	}

	/**
	 * finds an element file.
	 * project files are prefered over core files.
	 *
	 * @return string
	 */
	public static function get_element_file () {
		return self::get_project_file(
			Core::$configuration->directory->elements . $file, true
		);
	}

	/**
	 * finds a template file.
	 * project files are prefered over core files.
	 *
	 * @return string
	 */
	public static function get_template_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->templates . $file, true
		);
	}

	/**
	 * finds a log file
	 *
	 * @return string
	 */
	public static function get_log_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->logs . $file
		);
	}

	/**
	 * finds a model file
	 *
	 * @return string
	 */
	public static function get_model_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->models . $file, true
		);
	}

	/**
	 * finds a routing file
	 *
	 * @return string
	 */
	public static function get_routing_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->routing . $file, true
		);
	}

	/**
	 * finds a project configuration file
	 *
	 * @return string
	 */
	public static function get_configuration_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->configuration . $file .
			Core::$configuration->loading->conf
		);
	}

	/**
	 * finds a javascript file
	 *
	 * @return string
	 */
	public static function get_javascript_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->javascript . $file
		);
	}

	/**
	 * finds a css file
	 *
	 * @return string
	 */
	public static function get_css_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->css . $file
		);
	}

	/**
	 * finds an image file
	 *
	 * @return string
	 */
	public static function get_image_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->image . $file
		);
	}

	/**
	 * finds an include file
	 *
	 * @return string
	 */
	public static function get_include_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->include . $file
		);
	}
}
