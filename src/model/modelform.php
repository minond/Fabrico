<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\klass\DocParser;

class ModelForm {
	use DocParser;

	/**
	 * class name
	 * @var string
	 */
	private $baseclass;

	/**
	 * class reflection
	 * @var ReflectionClass
	 */
	private $reflection;

	/**
	 * class instance
	 * @var mixed
	 */
	private $instance;

	/**
	 * @param mixed $baseclass
	 */
	public function __construct($baseclass) {
		if (is_string($baseclass)) {
			$this->baseclass = $baseclass;
		}
		else {
			$this->set_model($baseclass);
			$this->baseclass = get_class($baseclass);
		}

		$this->reflection = new \ReflectionClass($baseclass);
	}

	/**
	 * @param mixed $instance
	 */
	public function set_model($instance) {
		$this->instance = $instance;
	}

	/**
	 * return class properties
	 * @return ReflectionProperty[]
	 */
	public function properties() {
		return array_filter($this->reflection->getProperties(), function(\ReflectionProperty $prop) {
			return !$prop->isStatic();
		});
	}

	/**
	 * @return ModelFormField[]
	 */
	public function fields() {
		$fields = [];

		foreach ($this->properties() as $prop) {
			$value = $this->get_value($prop);
			$name = $prop->getName();
			$doc = $this->property($this->baseclass, $name);
			$type = gettype($value);

			if (isset($doc['label'])) {
				$label = $doc['label'];
			}
			else {
				$label = ucwords(str_replace('_', ' ', $name));
			}

			$fields[] = new ModelFormField($name, $label, $type, $value);
		}

		return $fields;
	}

	/**
	 * value getter
	 * @param ReflectionProperty $prop
	 * @return mixed
	 */
	public function get_value(\ReflectionProperty $prop) {
		$prop->setAccessible(true);
		return $prop->getValue($this->instance);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$html = '';

		foreach ($this->fields() as $field) {
			$html .= (string) $field;
		}

		return $html;
	}
}
