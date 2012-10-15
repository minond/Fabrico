<?php

/**
 * Fabrico view helpers
 */

namespace view;

/**
 * returns the path to a template file
 *
 * @param string template name
 * @return string template path
 */
function template ($name, $building = false) {
	if (!$building) {
		if (is_object($name)) {
			$name = $name->file;
		}

		return \Fabrico\Page::get_template($name);
	}

	return \Fabrico\Project::find_file(
		\Fabrico\Core::$configuration->directory->templates . $name
	);
}

/**
 * includes an element file
 *
 * @param string element name*
 */
function element ($name) {
	for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
		$file;
		$arg = func_get_arg($i);

		if (is_object($arg)) {
			$file = $arg->file;
		}
		else {
			$file = $arg;
		}

		$file = \Fabrico\util::csv_string($file);

		for ($i = 0; $i < count($file); $i++) {
			include_once \Fabrico\Project::find_file(
				\Fabrico\Core::$configuration->directory->elements . $file[ $i ]
			);
		}
	}
}

/**
 * element parameter setter
 *
 * @param mixed value
 */
function param ($data) {
	\Fabrico\Element::argument($data);
}

/**
 * generates a div with a specific size
 *
 * @param integer height
 * @param integer width
 */
function space ($data) {
	echo \Fabrico\html::div([
		'style' => [
			'height' => isset($data->height) ? "{$data->height}px" : '0px',
			'width' => isset($data->width) ? "{$data->width}px" : '0px'
		]
	]);
}

/**
 * sends a location redirect header and kills the current process
 *
 * @param string uri
 */
function redirect ($uri) {
	header("Location: {$uri}");
	die;
}