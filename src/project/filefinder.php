<?php

/**
 * @package fabrico\project
 */
namespace fabrico\project;

/**
 * finds a loads project files (controllers, models, etc.)
 * TODO: depricate this
 */
interface FileFinder {
	/**
	 * used by FileLoader
	 */
	public static function get_project_file_type();
}
