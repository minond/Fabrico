<?php

class db {
	/**
	 * @name instance
	 * @var db instance
	 */
	private static $instance;

	/**
	 * @constructor
	 */
	private function __construct () {
		
	}

	/**
	 * @name init
	 *
	 * creates and saves a new db instance if needed
	 */
	public static function init () {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
	}
}


