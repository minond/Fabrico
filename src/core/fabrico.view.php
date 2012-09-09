<?php

namespace Fabrico;

class View {
	/**
	 * TODO: move view build and dispatch logic here
	 */
	public static function dispatch (& $_controller, $_file, $_build = false) {
		$_view = Project::get_view_file($_file);
		$_buildfile = Project::get_view_build_file($_file);

		if ($_build) {
			Page::build($_view);
		}

		require $_buildfile;

		if ($_build) {
			echo Page::close($_file);
		}
	}
}
