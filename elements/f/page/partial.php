<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\page;

use fabrico\output\View;
use fabrico\output\Build;
use fabrico\core\Project;

/**
 * template loader
 */
class Partial extends \fabrico\output\Tag {
	/**
	 * @var View
	 */
	private static $view;

	/**
	 * template arguments
	 * @var array
	 */
	private $t_args = [];

	/**
	 * template file name
	 * @var string
	 */
	public $file;

	/**
	 * overwrite to allow undefined variables
	 * @param string $var
	 * @param mixed $val
	 */
	public function set ($var, $val) {
		if (property_exists($this, $var)) {
			$this->{ $var } = $val;
		}
		else {
			$this->t_args[ $var ] = $val;
		}
	}

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		if (!self::$view) {
			self::$view = new View;
			self::$view->builder = new Build;
		}

		echo self::$view->get($this->file, Project::TEMPLATE, $this->t_args);
	}
}
