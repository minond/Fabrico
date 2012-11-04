<?php

/**
 * @package fabrico\controller
 */
namespace fabrico\controller;

use fabrico\core\util;
use fabrico\core\Project;
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

	/**
	 * load and setup a new controller
	 * @param string $controller
	 */
	public static function load ($controller) {
		$core = & self::getcore();
		$file = $core->project->get_file($controller, Project::CONTROLLER);

		require $file;
		$core->controller = new $controller;
	}
}
