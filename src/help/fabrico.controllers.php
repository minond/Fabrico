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
 * javascript method api controller
 */
interface PublicMethodController {
	/**
	 * called before requested method
	 */
	public function onmethod ($method, & $arguments);
}

/**
 * page view trait
 */
trait PageView {
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

		foreach ($this as $_var => $_val) {
			$$_var = $_val;
		}

		unset($_var);
		unset($_val);
		$_controller =& $this;

		ob_start();
		require Project::get_build_file_from_data();
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

		return Arbol::get($id);
	}
}

/**
 * page update trait
 */
trait PageUpdate {
	public function get_node_content ($ids) {
		$updates = [];

		foreach ($ids as $id) {
			$updates[ $id ] = $this->get_node($id)->props[ Element::A_CONTENT ];
		}

		return $updates;
	}
}
