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
class Script extends Tag {
	use Mediator;

	/**
	 * js file
	 * @var string
	 */
	public $file;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		$this->core->response->outputcontent->add_js_file(
			$this->core->project->get_resource($this->file, Project::JS)
		);
	}
}
