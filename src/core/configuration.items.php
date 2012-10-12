<?php

namespace fabrico;

class ConfigurationItems {
	/**
	 * @var array[string]string
	 */
	private $storage;

	/**
	 * @param array[string]string $props
	 */
	public function __construct (array $props) {
		$this->storage = $props;
	}

	/**
	 * getter shortcut
	 * @param string $prop
	 * @return string
	 */
	public function __get ($prop) {
		return array_key_exists($prop, $this->storage) ?
		       $this->storage[ $prop ] : null;
	}

	/**
	 * @param string $prop
	 * @param string $value
	 * @param boolean $allow_overwrite
	 */
	public function set ($prop, $value, $allow_overwrite = false) {
		if ($allow_overwrite || !array_key_exists($prop, $this->storage)) {
			$this->storage[ $prop ] = $value;
		}
	}
}
