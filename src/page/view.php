<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

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
		util::dpre("dispatching $file");
	}
}

$v = new View;
$v->builder = new Build;


