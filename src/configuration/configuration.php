<?php

/**
 * @package fabrico\configuration
 */
namespace fabrico\configuration;

use fabrico\core\util;
use fabrico\core\Core;
use fabrico\core\Module;
use fabrico\error\LoggedException;

/**
 * ConfigurationItems manager
 */
class Configuration extends Module {
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
			$hash = self::CACHE . $ns . $file;

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
}
