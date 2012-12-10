<?php

/**
 * @package fabrico\cache
 */
namespace fabrico\cache;

/**
 * @uses Session
 */
class Flash extends Cache {
	/**
	 * @var Session
	 */
	private static $cache;

	/**
	 * this session's identifier
	 * @var string
	 */
	private static $session_key;

	/**
	 * initialize flash storage
	 */
	public function __construct() {
		if (!self::$cache) {
			self::$cache = new Session;
			self::$session_key = uniqid();

			// initialize flash storage
			if (!self::$cache->has('flash-items')) {
				self::$cache->set('flash-items', []);
			}

			// get last session's key
			$last_session = self::$cache->get('flash-last-session');

			// save this session's key
			self::$cache->set('flash-last-session', self::$session_key);

			// and delete old values
			if ($last_session) {
				foreach (self::$cache->get('flash-items') as $key => $item) {
					if ($item['key'] !== $last_session) {
						unset(self::$cache->rget('flash-items')[ $key ]);
					}
				}
			}
		}
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function get($key) {
		return $this->has($key) ?
			self::$cache->get('flash-items')[ $key ]['val'] : null;
	}

	/**
	 * @param string $key
	 * @param string $val
	 * @return string
	 */
	public function set($key, $val) {
		self::$cache->rget('flash-items')[ $key ] = [
			'key' => self::$session_key,
			'val' => $val
		];
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return isset(self::$cache->rget('flash-items')[ $key ]);
	}
}
