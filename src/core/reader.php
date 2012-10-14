<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * file reader
 */
class Reader extends Module {
	/**
	 * yml file format reader
	 * @var callable
	 */
	private static $yml;

	/**
	 * @param callable $reader
	 */
	public static function yml (callable $reader) {
		self::$yml = $reader;
	}
}
