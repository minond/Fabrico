<?php

namespace Fabrico;

/**
 * returns the path to a template file
 *
 * @param string template name
 * @return string template path
 */
function template ($name) {
	return Project::find_file(Core::$configuration->directory->templates . $name);
}

/**
 * returns the path to an element file
 *
 * @param string element name
 * @return string element path
 */
function element ($name) {
	return Project::find_file(Core::$configuration->directory->elements . $name);
}
