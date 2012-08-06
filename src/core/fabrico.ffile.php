<?php

namespace Fabrico;

class FFile {
	/**
	 * requested file
	 *
	 * @var string
	 */
	public static $file;

	/**
	 * returns a path to a project file
	 *
	 * @param string file name
	 * @return string file path
	 */
	private static function get_project_file ($name) {
		return Core::$configuration->loading->prefix .
		       Core::$configuration->project . $name .
		       Core::$configuration->loading->suffix;
	}


	/**
	 * finds the view file for the current request
	 *
	 * @return string
	 */
	public static function get_view_file () {
		return self::get_project_file(
			Core::$configuration->directory->views .
			self::$file
		);
	}

	/**
	 * find the controller file for the current request
	 *
	 * @return string
	 */
	public static function get_controller_file () {
		$file = self::$file;

		return self::get_project_file(
			Core::$configuration->directory->controllers . $file
		);
	}

	/**
	 * finds an element file.
	 * project files are prefered over core files.
	 *
	 * @return string
	 */
	public static function get_element_file () {}

	/**
	 * finds a template file.
	 * project files are prefered over core files.
	 *
	 * @return string
	 */
	public static function get_template_file () {}

	/**
	 * finds a log file
	 *
	 * @return string
	 */
	public static function get_log_file () {}

	/**
	 * finds a model file
	 *
	 * @return string
	 */
	public static function get_model_file () {}

	/**
	 * finds a routing file
	 *
	 * @return string
	 */
	public static function get_routing_file () {}

	/**
	 * finds a project configuration file
	 *
	 * @return string
	 */
	public static function get_configuration_file () {}

	/**
	 * finds a javascript file
	 *
	 * @return string
	 */
	public static function get_javascript_file () {}

	/**
	 * finds a css file
	 *
	 * @return string
	 */
	public static function get_css_file () {}

	/**
	 * finds an image file
	 *
	 * @return string
	 */
	public static function get_image_file () {}

	/**
	 * finds an include file
	 *
	 * @return string
	 */
	public static function get_include_file () {}
}
