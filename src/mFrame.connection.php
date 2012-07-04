<?php

class db {
	private static $instance;

	private function __construct () {
		
	}

	public static function init () {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
	}
}


