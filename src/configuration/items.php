<?php

/**
 * @package fabrico\configuration
 */
namespace fabrico\configuration;

/**
 * ConfigurationItem manager
 */
class ConfigurationItems {
	/**
	 * @var array[string]ConfigurationItems
	 */
	private $items = [];

	/**
	 * @param string $item
	 * @param ConfigurationItem $ci
	 */
	public function set ($item, ConfigurationItem & $ci) {
		if (!array_key_exists($item, $this->items)) {
			$this->items[ $item ] = & $ci;
		}
	}

	/** 
	 * @param string $item
	 * @return ConfigurationItem
	 */
	public function __get ($item) {
		return array_key_exists($item, $this->items) ?
		       $this->items[ $item ] : null;
	}
}
