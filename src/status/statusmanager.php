<?php

/**
 * @package fabrico\status
 */
namespace fabrico\status;

/**
 * status manager
 */
class StatusManager {
	const OK = 'ok';
	const ERROR = 'error';

	/**
	 * not singletons, but act like a namespace
	 */
	private function __construct() {}

	/**
	 * @return array
	 */
	public static function gets() {
		return [];
	}
}
