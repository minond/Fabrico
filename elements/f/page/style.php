<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\page;

use fabrico\output\Tag;
use fabrico\core\Mediator;
use fabrico\core\Project;

/**
 * project script loader
 */
class Style extends Tag {
	use Mediator;

	/**
	 * css file
	 * @var string
	 */
	public $file;

	/**
	 * is core (fabrico) css file
	 * @var boolean
	 */
	public $internal = false;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		if ($this->file) {
			$this->core->response->outputcontent->add_css_file(
				$this->core->project->get_resource(
					$this->file, Project::CSS, $this->internal
				)
			);
		}
		else if ($this->__content) {
			$this->core->response->outputcontent->add_css_code($this->__content);
		}
	}
}
