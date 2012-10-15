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
	 *
	 * @param boolean $check_routing
	 */
	public static function set_files ($check_routing = true) {
		self::$file = Router::get_file_requested(true);

		// custom routes
		if ($check_routing) {
			Router::check_project_routing();
		}

		if (is_dir(self::get_view_file(true))) {
			self::$file .= '/' . Core::$configuration->convention->index_file;
		}

		Core::$configuration->state->guid = uniqid();
		Core::$configuration->state->uri = self::$file;
		Core::$configuration->state->view = self::get_view_file();
		Core::$configuration->state->raw_view = self::get_view_file(true);
		Core::$configuration->state->controller = self::get_controller_file();
		Core::$configuration->state->build = self::get_view_build_file(self::$file);
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
	 * @param raw request
	 * @return string
	 */
	public static function get_view_file ($raw = false) {
		return self::get_project_file(
			Core::$configuration->directory->views .
			self::$file, !$raw
		);
	}

	/**
	 * find the controller file for the current request
	 *
	 * @return object with controller path and name
	 */
	public static function get_controller_file ($name = false) {
		if ($name) {
			return self::get_project_file(
				Core::$configuration->directory->controllers . $name, true
			);
		}

		$parts = explode('/', explode('.', self::$file)[ 0 ]);
		$possibilities = [];

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

		$controller->controller_real_name = "\\{$controller->controller_name}";
		
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
			Core::$configuration->directory->logs . $file .
			Core::$configuration->loading->log
		);
	}

	/**
	 * finds the project model directory
	 *
	 * @return string
	 */
	public static function get_model_directory () {
		return self::get_project_file(
			Core::$configuration->directory->models
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
	 * @param boolean Yaml file
	 * @return string
	 */
	public static function get_configuration_file ($file, $yml = true) {
		return self::get_project_file(
			Core::$configuration->directory->configuration . $file .
			($yml ? Core::$configuration->loading->conf : Core::$configuration->loading->suffix)
		);
	}

	/**
	 * finds a javascript file
	 *
	 * @param string file name
	 * @param boolean internal file flag
	 * @return string
	 */
	public static function get_javascript_file ($file, $internal = false) {
		if ($internal) {
			return self::get_core_resource_file(
				Core::$configuration->directory->javascript . $file
			);
		}
		else {
			return self::get_project_file(
				Core::$configuration->directory->javascript . $file
			);
		}
	}

	/**
	 * finds a css file
	 *
	 * @param string file name
	 * @param boolean internal file flag
	 * @return string
	 */
	public static function get_css_file ($file, $internal = false) {
		if ($internal) {
			return self::get_core_resource_file(
				Core::$configuration->directory->css . $file
			);
		}
		else {
			return self::get_project_file(
				Core::$configuration->directory->css . $file
			);
		}
	}

	/**
	 * finds an image file
	 *
	 * @param string file name
	 * @param boolean internal file flag
	 * @return string
	 */
	public static function get_image_file ($file, $internal = false) {
		if ($internal) {
			return self::get_core_resource_file(
				Core::$configuration->directory->image . $file
			);
		}
		else {
			return self::get_project_file(
				Core::$configuration->directory->image . $file
			);
		}
	}

	/**
	 * returns the path to a project resource file
	 *
	 * @param string file name
	 * @return string full file path
	 */
	public static function get_resource_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->resource . $file
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

	/**
	 * returns the paths to a view's build file
	 *
	 * @param string file name
	 * @return string build file path
	 */
	public static function get_view_build_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->build . 
			Core::$configuration->directory->views . $file .
			Core::$configuration->loading->suffix
		);
	}

	/**
	 * returns the paths to a template's build file
	 *
	 * @param string file name
	 * @return string build file path
	 */
	public static function get_template_build_file ($file) {
		return self::get_project_file(
			Core::$configuration->directory->build . 
			Core::$configuration->directory->templates . $file .
			Core::$configuration->loading->suffix
		);
	}

	/**
	 * looks in the current project and core directories for a given file.
	 * project directories are preferred over core files
	 *
	 * @param string file name
	 * @return string file path
	 */
	public static function find_file ($file) {
		$project = self::get_project_file($file . Core::$configuration->loading->suffix);

		if (!file_exists($project)) {
			$core = self::get_core_file($file);

			if (file_exists($core)) {
				$project = $core;
			}
		}

		return $project;
	}

	/**
	 * returns the path to a core file
	 *
	 * @param string file name
	 * @return string file path
	 */
	public static function get_core_file ($file) {
		return Core::$configuration->loading->core . $file .
		       Core::$configuration->loading->suffix;
	}

	/**
	 * returns the path to an internal core file
	 *
	 * @param string file name
	 * @return string file path
	 */
	public static function get_core_resource_file ($file) {
		return Core::$configuration->loading->internal . $file;
	}

	/**
	 * returns the path to the build file
	 * used for data requests
	 *
	 * @return string view build file path
	 */
	public static function get_build_file_from_data () {
		return str_replace([ '.json', '.xml', '.js', '.pdf' ], '', Core::$configuration->state->build);
	}

	/**
	 * returns the path to a file without data extensions
	 *
	 * @param string $file
	 * @return string
	 */
	public static function get_file_no_data ($file) {
		return str_replace([ '.json', '.xml', '.js', '.pdf' ], '', $file);
	}

	/**
	 * returns the path to an internal dependency file
	 *
	 * @param string $name
	 * @return string name
	 */
	public static function get_dependency_file ($name) {
		return Core::$configuration->loading->httproot .
		       Core::$configuration->loading->internal .
		       Core::$configuration->directory->dependency . $name;
	}
}