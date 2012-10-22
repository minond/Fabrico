<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Project;
use fabrico\core\Module;
use fabrico\core\util;
use fabrico\page\Tag;

/**
 * view dispatcher
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
	 * @param string $self
	 */
	private function inc_file ($self) {
		require $self;
	}

	/**
	 * @param string $file 
	 * @param string $type 
	 */
	public function dispatch ($file, $type) {
		$view = $this->core->project->get_file($file, $type);
		$build = $this->core->project->get_build($file, $type);
		$ready = $this->request_build($view, $build);

		if ($ready) {
			$this->inc_file($build);
		}
	}
}