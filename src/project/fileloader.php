<?php

/**
 * @package fabrico\project
 */
namespace fabrico\project;

/**
 * finds a loads project files (controllers, models, etc.)
 * TODO: deprivate this
 */
trait FileLoader {
	/**
	 * @param mixed $identifier
	 * @return string
	 */
	public static function parse_project_file_name($identifier) {
		return $identifier;
	}

	/**
	 * @param mixed $identifier
	 * @param boolean $versioned
	 * @return string
	 */
	public static function find_project_file($identifier, $versioned = false) {
		$elfile = self::parse_project_file_name($identifier);

		list($projectfile, $in_project) = self::getcore()->project->got_file(
			$elfile, self::get_project_file_type()
		);

		list($fabricofile, $in_fabrico) = self::getcore()->project->got_project_file(
			$elfile, self::get_project_file_type(),
			self::getcore()->configuration->core->file->to->internal_directory
		);

		if ($in_project) {
			$elfile = $projectfile;
		}
		else if ($in_fabrico) {
			$elfile = $fabricofile;
		}
		else {
			$elfile = null;
		}

		return $elfile;
	}

	/**
	 * @param mixed $identifier
	 * @return string
	 */
	public static function load_project_file($identifier) {
		$file = self::find_project_file($identifier);

		if (!is_null($file)) {
			require_once $file;
		}

		return $file;
	}
}
