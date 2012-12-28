<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\output\Html;

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
	 * field's placeholder
	 * @var string
	 */
	private $default;

	/**
	 * select field options
	 * @var array
	 */
	private $options;

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $type
	 * @param string $value
	 * @param string $default
	 * @param array $options
	 */
	public function __construct($name, $label, $type, $value = '', $default = '', $options = []) {
		$this->type = $type;
		$this->value = $value;
		$this->name = $name;
		$this->label = $label;
		$this->default = $default;
		$this->options = $options;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	private function wrap_field($content) {
		return $this->html('span', [
			'class' => 'scaffold_field_holder'
		], $content);
	}

	/**
	 * @param string $value
	 * @param string $label
	 * @param boolean $selected
	 * @return string
	 */
	private function option($value, $label = '', $selected = false) {
		$props = [];
		$props['value'] = $value;

		if ($selected) {
			$props['selected'] = 'true';
		}

		return $this->html(
			'option', $props,
			$label ? $label : $value
		);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$html = '';
		$options = '';
		$id = $this->name . mt_rand();

		$label = $this->html('label', [
			'for' => $id,
		], $this->label);

		switch ($this->type) {
			// invalid types
			case "object":
			case "resouce":
			case "unknown type":
				break;

			// special type
			case "array":
				if ($this->default) {
					if (!in_array($this->default, $this->options)) {
						array_unshift($this->options, $this->default);
					}
					else if (!$this->value) {
						$this->value = $this->default;
					}
				}

				foreach ($this->options as $option) {
					$options .= $this->option($option, $option, $this->value == $option);
				}

				$html = $this->wrap_field($label . $this->html('select', [
					'id' => $id,
					'name' => $this->name,
				], $options));

				break;

			case "boolean":
				// $options .= $this->option('1', 'Yes', $this->value);
				// $options .= $this->option('0', 'No', !$this->value);

				// $html = $this->wrap_field($label . $this->html('select', [
					// 'id' => $id,
					// 'name' => $this->name,
				// ], $options));

				$props = [
					'type' => 'checkbox',
					'id' => $id,
					'name' => $this->name,
				];

				if ($this->value === true) {
					$props['checked'] = true;
				}

				$html = $this->wrap_field($label . $this->html('input', $props));

				break;

			case "hidden":
				$html = $this->html('input', [
					'type' => 'hidden',
					'id' => $id,
					'name' => $this->name,
					'value' => $this->value,
				]);

				break;

			case "textarea":
				$html = $this->wrap_field($label . $this->html('textarea', [
					'id' => $id,
					'name' => $this->name,
				], $this->value));

				break;

			case "integer":
			case "double":
			case "string":
			default:
				$html = $this->wrap_field($label . $this->html('input', [
					'type' => 'text',
					'id' => $id,
					'name' => $this->name,
					'value' => $this->value,
				]));

				break;
		}

		return $html;
	}
}
