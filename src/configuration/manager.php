<?php

namespace fabrico;

class ConfigurationManager extends Module {
	/** 
	 * @param array[string]ConfigurationItems
	 */
	private $confs = [];

	/** 
	 * @param string $conf
	 * @param ConfigurationItems $ci
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
