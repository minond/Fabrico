<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\core\LightMediator;
use fabrico\project\Project;
use fabrico\project\FileFinder;
use fabrico\project\FileLoader;

abstract class Model implements FileFinder {
	use GetSet, FileLoader, LightMediator;

	public static function get_project_file_type() {
		return Project::MODEL;
	}
}
