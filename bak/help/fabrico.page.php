<?php

/**
 * Fabrico controller helpers
 */

namespace Fabrico\Page;

/**
 * page view trait
 */
trait Access {
	/**
	 * page content
	 * @var string
	 */
	private $__content = false;

	/**
	 * loads the build file and returns its content
	 *
	 * @return string build file html
	 */
	private function load_view () {
		if ($this->__content !== false) {
			return $this->__content;
		}

		$_controller =& $this;

		ob_start();
		require \Fabrico\Project::get_build_file_from_data();
		$this->__content = ob_get_clean();

		return $this->__content;
	}

	/**
	 * returns a node by its id
	 *
	 * @param string node id
	 * @return Arbol node
	 */
	private function get_node ($id = '') {
		if ($this->__content === false) {
			$this->load_view();
		}

		return \Fabrico\Arbol::get($id);
	}

	/**
	 * returns the contend for a list of components
	 */
	public function get_node_content ($ids) {
		$updates = [];

		foreach ($ids as $id) {
			$updates[ $id ] = $this->get_node($id)->props->content;
		}

		return $updates;
	}
}
