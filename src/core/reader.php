<?php

namespace fabrico;

class Reader extends Module {
	/**
	 * yml file format reader
	 * @var callable
	 */
	private static $yml;

	public static function yml (callable $reader) {
		self::$yml = $reader;
	}
}
