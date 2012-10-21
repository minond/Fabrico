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
	public function dispatch ($file) {
		$path = $this->core->project->get_file($file, Project::VIEW);
		util::dpre("dispatching $path");
	}
}

$p = new Project;
$p->get_file("test", Project::VIEW);

$v = new View;
$v->builder = new Build;
$v->dispatch("index");
