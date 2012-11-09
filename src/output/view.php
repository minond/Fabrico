<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\Project;
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
	 */
	public function retrive ($file, $type) {
		$view = $this->core->project->get_file($file, $type);
		$build = $this->core->project->get_build($file, $type);
		$ready = $this->request_build($view, $build);

		if ($ready) {
			$this->load_template($build);
		}
	}

	/**
	 * @param string $file
	 */
	public function load_template ($file) {
		$core = & $this->core;
		require $file;
	}

	/**
	 * @param string $file 
	 * @param string $type 
	 * @return string
	 */
	public function get ($file, $type) {
		ob_start();
		$this->retrive($file, $type);
		return ob_get_clean();
	}
}
