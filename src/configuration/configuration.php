<?php

/**
 * @package fabrico\configuration
 */
namespace fabrico\configuration;

use fabrico\core\util;
use fabrico\core\core;
use fabrico\core\Module;
use fabrico\error\LoggedException;

/**
 * ConfigurationItems manager
 */
class Configuration extends Module {
	/**
	 * conventions
	 */
	const CORE = 'core';
	const HTTPCONF = '../../configuration/httpconf.yml';

	/**
	 * use APC to cache configuration settings
	 */
	const APC = 'apc_cache';

	/**
	 * cached variable prefix
	 */
	const CACHE = '-';

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

	/**
	 * @param string $ns
	 * @param string $file
	 * @return ConfigurationItems
	 */
	private function read_and_load ($ns, $file) {
		$raw = $this->getc()->reader->yml($file);
		$items = new ConfigurationItems;

		foreach ($raw as $item => $confs) {
			$items->set($item, new ConfigurationItem($confs));
		}

		$this->set($ns, $items);
		return $items;
	}

	/**
	 * generate a cache hash name
	 * @param string $ns
	 * @param string $file
	 * @return string
	 */
	private function get_hash ($ns, $file) {
		return self::CACHE . $ns . $file;
	}

	/**
	 * load a configuration. configuration content can optionally
	 * be saved to (and read from) APC
	 * @param string $ns
	 * @param string $file
	 * @param string $cache
	 */
	public function load ($ns, $file, $cache = null) {
		if (!$cache) {
			$this->read_and_load($ns, $file);
		}
		else {
			$hash = $this->get_hash($ns, $file);

			switch ($cache) {
				case self::APC:
					if (apc_exists($hash)) {
						$conf = apc_fetch($hash);
						$this->set($ns, $conf);
					}
					else {
						apc_add($hash, $this->read_and_load($ns, $file));
					}

					break;

				default:
					throw new LoggedException("Invalid cache type: {$cache}");
			}
		}
	}

	/**
	 * clears cached configuration
	 * @param string $ns
	 * @param string $file
	 * @param string $cache
	 */
	public function clear ($ns, $file, $cache) {
		$hash = $this->get_hash($ns, $file);

		switch ($cache) {
			case self::APC:
				apc_delete($hash);
				break;
		}
	}
}
