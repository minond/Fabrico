<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use \fabrico\output\Html;

class ModelFormField {
	use Html;

	/**
	 * field's name property
	 * @var string
	 */
	private $name;

	/**
	 * field's label text
	 * @var string
	 */
	private $label;

	/**
	 * field value type
	 * @var string
	 */
	private $type;

	/**
	 * field's value
	 * @var string
	 */
	private $value;

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $type
	 * @param string $value
	 */
	public function __construct($name, $label, $type, $value = '') {
		$this->type = $type;
		$this->value = $value;
		$this->name = $name;
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$html = '';
		$id = $this->name . mt_rand();

		switch ($this->type) {
			case "integer":
			case "double":
			case "string":
				$label = $this->html('label', [
					'for' => $id,
				], $this->label);

				$field = $this->html('input', [
					'type' => 'text',
					'id' => $id,
					'name' => $this->name,
					'value' => $this->value,
				]);

				$html = $this->html('span', [
					'class' => 'scaffold_field_holder'
				], $label . $field);

				break;

			// special type
			case "array":
				break;

			// invalid types
			case "object":
			case "resouce":
			case "unknown type":
			default:
				break;
		}

		return $html;
	}
}
