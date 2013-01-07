<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output\page;

use fabrico\output\Tag;
use fabrico\controller\Controller;
use fabrico\core\Mediator;
use fabrico\core\util;
use fabrico\output\Page;
use fabrico\status\ControllerStatus;

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
	 * element verion
	 * @var int
	 */
	public $version = 0;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		if ($this->controller) {
			// load the controller
			$this->core->loader->load('controller');
			Controller::load($this->controller);

			if ($this->core->response->outputcontent instanceof Page) {
				$this->core->response->outputcontent->declare_var(
					'fabrico.information.controller',
					$this->controller, Page::STR
				);
				$this->core->response->outputcontent->declare_var(
					'fabrico.controller.status',
					ControllerStatus::gets(), Page::JSON
				);
			}
		}

		if (isset($this->version)) {
			$this->core->response->outputcontent->version = $this->version;
		}

		if ($this->format) {
			util::dpr($this->core->response);
			util::dpr($this->format);
		}
	}
}
