<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Module;

/**
 * represents any type of output content
 * ie. HTML, PDF, text, javascript, json, etc.
 */
abstract class OutputContent extends Module {
	/**
	 * view manager
	 * @var View
	 */
	public $view;

	/**
	 * called once ready to output content
	 * @param mixed $info
	 * @return string
	 */
	abstract public function render ($info);
}
