<?php

/**
 * @package fabrico\configuration
 */
namespace fabrico\configuration;

/**
 * json reader interface
 */
interface ConfigurationReader {
	/**
	 * called by the configuration manager when loading
	 * configuration from source into cache
	 * @param mixed array|stdClass $json
	 */
	public function load ($json);
}
