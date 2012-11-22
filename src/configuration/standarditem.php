<?php

/**
 * @package fabrico\configuration
 */
namespace fabrico\configuration;

use fabrico\configuration\ConfigurationReader;

/**
 * simple key/value pair information
 */
class StandardItem implements ConfigurationReader {
	/**
	 * @var stdClass[]
	 */
	private $items = [];

	/**
	 * @param stdClass $json
	 */
	public function load ($json) {
		foreach ($json as $name => $item) {
			$this->items[ $name ] = (object) $item;
		}

		return $this;
	}

	/**
	 * item getter
	 * @param string $item
	 */
	public function __get ($name) {
		return isset($this->items[ $name ]) ?
			$this->items[ $name ] : null;
	}
}
