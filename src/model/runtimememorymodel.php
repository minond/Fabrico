<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\cache\RuntimeMemory;

/**
 * model that saved data to the session
 */
class RuntimeMemoryModel extends AbstractModel {
	final protected static function initialize () {
		if (!self::$cache) {
			self::$cache = new RuntimeMemory;
		}
	}
}
