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
 *
 * Model annotations
 * @label (class) - form label, uses status merge field
 * @label (property) - field label
 * @enum (property) - select field options
 * @default (property) - default field value
 * @field (property) - force a field type
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
		Tag::load('f/page/style');

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

		$header = $this->html('div', [
			'class' => 'scaffold_form_header'
		], $title);

		$submit = $this->html('input', [
			'value' => 'Submit',
			'class' => 'scaffold_form_submit form_submit',
			'type' => 'submit'
		]);

		$reset = $this->html('input', [
			'value' => 'Cancel',
			'class' => 'scaffold_form_cancel form_reset',
			'type' => 'reset'
		]);

		$footer = $this->html('div', [
			'class' => 'scaffold_form_footer',
		], $reset . $submit);

		$content = $this->html('div', [
			'class' => 'scaffold_form_content'
		], $form);

		$this->add_class('scaffold_form');
		$this->set_content($header . $content . $footer);

		// load the css
		(string) new Style([
			'file' => 'scaffolds/form.css',
			'internal' => true
		]);

		(string) new Style([
			'file' => 'form/button.css',
			'internal' => true
		]);
	}
}
