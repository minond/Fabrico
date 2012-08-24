<?php

/**
 * Fabrico controller helpers
 */

namespace Fabrico;

/**
 * data controller interface
 */
interface DataRequestController {
	/**
	 * called before data specific getter method
	 */
	public function ondata ($type);
}

/**
 * page view trait
 */
trait PageView {
	/**
	 * loads the build file and returns its content
	 *
	 * @return string build file html
	 */
	private function load_data_build_file () {
		ob_start();
		require Project::get_build_file_from_data();
		return ob_get_clean();
	}

	/**
	 * returns a node by its id
	 *
	 * @param string node id
	 * @return Arbol node
	 */
	private function get_node ($id = '') {
		return Arbol::get($id);
	}
}
