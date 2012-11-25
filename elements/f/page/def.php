<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\page;

use fabrico\output\Tag;
use fabrico\controller\Controller;
use fabrico\core\Mediator;
use fabrico\core\util;

/**
 * page definition/information
 */
class Def extends Tag {
	use Mediator;
	
	/**
	 * page's controller
	 */
	public $controller;

	/**
	 * available page formats. space separated
	 * ie. html text pdf json
	 */
	public $format;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		if ($this->controller) {
			// load the controller
			$this->core->loader->load('controller');
			Controller::load($this->controller);
		}   

		if ($this->format) {
			util::dpr($this->core->response);
			util::dpr($this->format);
		}   
	}   
}
