<?php

/**
 * returns the path to a template file
 *
 * @param string template name
 * @return string template path
 */
function template ($name) {
	return Fabrico\Project::find_file(
		Fabrico\Core::$configuration->directory->templates . $name
	);
}

/**
 * includes an element file
 *
 * @param string element name*
 */
function element ($name) {
	for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
		include Fabrico\Project::find_file(
			Fabrico\Core::$configuration->directory->elements .
			func_get_arg($i)
		);
	}
}
