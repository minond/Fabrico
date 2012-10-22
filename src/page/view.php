<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Project;
use fabrico\core\Module;
use fabrico\core\util;

/**
 * view dispatcher
 */
class View extends Module {
	/**
	 * @var Build
	 */
	public $builder;

	/**
	 * @param string $file
	 */
	public function dispatch ($file, $type) {
		$viewr = $this->core->project->get_file($file, $type);
		$build = $this->core->project->get_build($file, $type);

		if ($this->builder->can_build()) {
			if ($this->builder->should_build([$viewr], $build)) {
				$success = $this->builder->build([$viewr], $build);

				if (!$success) {
					echo "error while building view";
				}
				else {
					echo "view built!";
				}
			}
		}
		
		util::dpre("dispatching [$type] $viewr, $build");
	}
}

$v = new View;
$v->builder = new Build;
$v->dispatch("index", Project::VIEW);
