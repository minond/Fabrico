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
	public $name;
	public function __construct () {
		$this->name = get_class($this);
	}
	public function name () { return "~~{$this->name}"; }
}
