<?php

namespace Fabrico;

class Build {
	/**
	 * creates a directory (and child directories) in the build directory
	 *
	 * @param string file path
	 */
	private static function makedir ($filepath) {
		$parts = explode('/', $filepath);
		array_pop($parts);
		$path = implode(DIRECTORY_SEPARATOR, $parts);

		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
	}

	/**
	 * parses a file's raw content and saves it in a build file
	 *
	 * @param string file path
	 * @param string raw content
	 */
	private static function writeclean ($filepath, $content) {
		$file = fopen($filepath, 'w+');
		$clean = Tag::parse($content);
		$clean = Merge::output_placeholder($clean);

		if (is_resource($file)) {
			Logger::build('savind build for ' . Core::$configuration->state->view);
			fwrite($file, $clean);
			fclose($file);
		}
	}

	/**
	 * returns true if the project file needs to be re-parsed
	 *
	 * @param string user file path
	 * @param string build file path
	 * @return boolean
	 */
	private static function build_needed ($userf, $buildf) {
		return file_exists($buildf) ? filemtime($userf) > filemtime($buildf) : true;
	}

	/**
	 * builds a new view file is it is needed
	 *
	 * @param string file name
	 * @param string raw file content
	 */
	public static function view ($file, $rawcontent) {
		$viewf = Project::get_view_file();
		$buildf = Project::get_view_build_file($file);
		
		if (self::build_needed($viewf, $buildf)) {
			self::makedir($buildf);
			self::writeclean($buildf, $rawcontent);
		}
	}
}
