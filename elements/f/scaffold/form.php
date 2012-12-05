<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\scaffold;

use fabrico\output\Tag;
use fabrico\model\ModelForm;
use fabrico\output\f\page\Style;

/**
 * ModelForm wrapper
 */
class Form extends Tag {
	protected static $tag = 'form';

	/**
	 * @var Model
	 */
	public $model;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		$this->set_content(new ModelForm($this->model));

		// load the css
		Tag::load('f/page/style');
		(string) new Style([
			'file' => 'scaffolds/form.css',
			'internal' => true
		]);
	}
}
