<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\page;

use fabrico\output\Tag;
use fabrico\core\Mediator;
use fabrico\project\Project;

/**
 * project script loader
 */
class Script extends Tag {
	use Mediator;

	/**
	 * js file
	 * @var string
	 */
	public $file;

	/**
	 * is core (fabrico) js file
	 * @var boolean
	 */
	public $internal = false;

	/**
	 * execute code on page load
	 * @var boolean
	 */
	public $onload = false;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		if ($this->file) {
			$this->core->response->outputcontent->add_js_file(
				$this->core->project->get_resource(
					$this->file, Project::JS, $this->internal
				)
			);
		}
		else if ($this->__content) {
			if ($this->onload)
				$this->core->response->outputcontent->add_js_load($this->__content);
			else
				$this->core->response->outputcontent->add_js_code($this->__content);
		}
	}
}
