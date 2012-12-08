<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\scaffold;

use fabrico\core\util;
use fabrico\output\Tag;
use fabrico\model\ModelForm;
use fabrico\output\f\page\Style;
use fabrico\klass\DocParser;

/**
 * ModelForm wrapper
 */
class Form extends Tag {
	use DocParser;

	/**
	 * @see Tag::$tag
	 */
	protected static $tag = 'form';

	/**
	 * @var Model
	 */
	public $model;

	/**
	 * form title
	 * @var string
	 */
	public $title;

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		$form = new ModelForm($this->model);
		$docs = $this->klass($this->model);
		$status = isset($this->model->id) ? 'Edit' : 'Add';

		$class = get_class($this->model);
		$class = explode('\\', $class);
		$class = $class[ count($class) - 1 ];

		if (strlen($this->title)) {
			$title = util::merge($this->title, [
				'status' => $status
			]);
		}
		else if (isset($docs['label'])) {
			$title = util::merge($docs['label'], [
				'status' => $status
			]);
		}
		else {
			$title = sprintf('%s %s', $status, $class);
		}

		$title = $this->html('div', [
			'class' => 'scaffold_form_title'
		], $title);

		$submit = $this->html('input', [
			'value' => 'Submit',
			'class' => 'scaffold_form_submit',
			'type' => 'submit'
		]);

		$reset = $this->html('input', [
			'value' => 'Cancel',
			'class' => 'scaffold_form_cancel',
			'type' => 'reset'
		]);

		$this->add_class('scaffold_form');
		$this->set_content($title . $form . $submit . $reset);

		// load the css
		Tag::load('f/page/style');
		(string) new Style([
			'file' => 'scaffolds/form.css',
			'internal' => true
		]);
	}
}
