<?php

/**
 * standard handling of http requests
 */
namespace fabrico\configuration;

use fabrico\core\Module;
use fabrico\cache\Cache;
use fabrico\error\LoggedException;

/**
 * manages all configuration items
 */
class ConfigurationManager extends Module {
	/**
	 * configuration storage
	 * @var Cache
	 */
	private $cache;

	/**
	 * items acess
	 * @var JsonReader[]
	 */
	private $items = [];

	/**
	 * item has format
	 * @var string
	 */
	private $hash = 'configuration-item-%s';

	/**
	 * @param Cache $cache
	 */
	public function __construct ($cache) {
		if ($cache instanceof Cache) {
			$this->cache = $cache;
		}
		else {
			throw new LoggedException('Unknown cache system');
		}
	}

	/**
	 * items access
	 * @param string $what
	 * @return JsonReader
	 */
	public function __get ($what) {
		return isset($this->items[ $what ]) ?
			$this->items[ $what ] : null;
	}

	/**
	 * load a configuration item, returns success
	 * @param string $what
	 * @param string $from
	 * @param ConfigurationReader $as
	 * @return boolean
	 */
	public function load ($what, $from, ConfigurationReader $as) {
		$hash = sprintf($this->hash, $what);

		if ($this->cache->has($hash)) {
			$this->items[ $what ] = $this->cache->get($hash);
		}
		else {
			if (file_exists($from)) {
				$this->items[ $what ] = $as->load(json_decode(file_get_contents($from)));
				$this->cache->set($hash, $this->items[ $what ]);
			}
			else {
				throw new LoggedException("Configuration file not found: $from");
			}
		}
	}
}
