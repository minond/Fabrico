<?php

/**
 * @package fabrico\fs
 */
namespace fabrico\fs;

use fabrico\core\LightMediator;
use fabrico\project\Project;

/**
 * project file cache helper
 */
trait Cache {
	use LightMediator {
		getc    as __getc;
		getcore as __getcore;
	}

	/**
	 * save a cache file, returns full path to cache file
	 * @param string $filename
	 * @param array $contens
	 * @return string
	 */
	private function jcsave($filename, array $contens) {
		$filepath = $this->__getc()->project->get_file(
			$filename,
			Project::CACHE
		);

		$parts = explode(DIRECTORY_SEPARATOR, $filepath);
		array_pop($parts);
		$path = implode(DIRECTORY_SEPARATOR, $parts);

		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}

		touch($filepath);
		file_put_contents($filepath, json_encode($contens));

		return $filepath;
	}

	/**
	 * read a cache file, returns false if file does not exits or is empty
	 * @param string $filename
	 * @return mixed boolean|object
	 */
	private function jcread($filename) {
		$cache = false;
		$filepath = $this->__getc()->project->get_file(
			$filename,
			Project::CACHE
		);

		if (file_exists($filepath)) {
			$content = file_get_contents($filepath);

			if ($content) {
				$cache = json_decode($content);
			}
		}

		return $cache;
	}
}
