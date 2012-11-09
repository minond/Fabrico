<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

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
