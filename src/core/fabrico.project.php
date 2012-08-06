<?php

namespace Fabrico;

class Project {
	/**
	 * loads the view and controller files into the router
	 */
	public static function set_files () {
		$view = FFile::get_view_file();
		$controller = FFile::get_controller_file();
		Router::set_files($view, $controller);
	}
}
