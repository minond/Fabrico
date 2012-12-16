<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\project\Project;
use fabrico\core\Module;
use fabrico\core\util;
use fabrico\output\Tag;

/**
 * view retriever
 */
class View extends Module {
	/**
	 * @var Build
	 */
	public $builder;

	/**
	 * @param string $raw
	 * @param string $build
	 */
	private function request_build ($raw, $build) {
		$built = true;

		if ($this->builder->can_build()) {
			if ($this->builder->should_build([ $raw ], $build)) {
				$built = $this->builder->build([ $raw ], $build);
			}
		}

		return $built;
	}

	/**
	 * @param string $file
	 * @param string $type
	 * @param array $args
	 */
	public function retrive ($file, $type, array & $args = null) {
		$view = $this->core->project->get_file($file, $type);
		$build = $this->core->project->get_build($file, $type);
		$ready = $this->request_build($view, $build);

		if ($ready) {
			$this->load_template($build, $args);
		}
	}

	/**
	 * @param string $file
	 * @param array $args
	 */
	public function load_template ($file, array & $args = null) {
		if (is_array($args)) {
			foreach ($args as $var => $val) {
				$$var = $val;
			}
		}

		$controller = & $this->core->controller;
		require $file;
	}

	/**
	 * @param string $file
	 * @param string $type
	 * @param array $args
	 * @return string
	 */
	public function get ($file, $type, array & $args = null) {
		ob_start();
		$this->retrive($file, $type, $args);
		return ob_get_clean();
	}
}
