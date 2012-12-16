<?php

/**
 * @package fabrico\project
 */
namespace fabrico\project;

/**
 * finds a loads project files (controllers, models, etc.)
 */
interface FileFinder {
	/**
	 * @param mixed $identifier
	 * @return string
	 */
	public static function find_project_file($identifier);

	/**
	 * @param mixed $identifier
	 * @return string
	 */
	public static function load_project_file($identifier);
}
