<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\util;
use fabrico\core\Mediator;
use fabrico\core\Project;
use fabrico\error\LoggedException;
use fabrico\output\View;
use fabrico\output\Build;
use fabrico\controller\Controller;

class Text extends Tag {
	protected static $tag = 'input';
	protected static $tagopt = [ 'name', 'user' => 'data-us', 'value' ];
	public $name;
	public $user;
	public $value;
	protected function initialize () {
		$this->name .= "_input_field";
	}
}

class Block extends Tag {
	protected static $tag = 'div';
	protected static $tagopt = ['id'];
	protected function initialize () {
		//$this->id = '~~' . $this->__args[ count($this->__args) - 1 ]->value;
	}
}

class Arg extends Tag {
	protected static $arg = true;
	public $name;
	public $value;
}
