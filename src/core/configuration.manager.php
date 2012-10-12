<?php

namespace fabrico;

class ConfigurationManager extends Module {
	/**
	 * @var array[string]ConfigurationManager
	 */
	private $confs = [];

	/**
	 * @param string $name
	 * @param ConfigurationItems $item
	 */
	public function set ($conf, ConfigurationItems & $ci) {
		if (!array_key_exists($conf, $this->confs)) {
			$this->confs[ $conf ] = & $ci;
		}
	}

	/** 
	 * @param string $conf
	 * @return ConfigurationItems
	 */
	public function __get ($conf) {
		return array_key_exists($conf, $this->confs) ?
		       $this->confs[ $conf ] : null;
	}
}
