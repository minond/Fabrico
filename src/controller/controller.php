<?php

/**
 * @package fabrico\controller
 */
namespace fabrico\controller;

use fabrico\core\Module;

/**
 * controller base
 */
class Controller extends Module {
	public function __construct () {
		echo "crateing a new " . get_class($this);
	}
}
