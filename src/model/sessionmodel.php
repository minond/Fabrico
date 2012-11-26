<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

use fabrico\cache\Session;

/**
 * model that saved data to the session
 */
class SessionModel extends AbstractModel {
	final protected static function initialize () {
		if (!self::$cache) {
			self::$cache = new Session;
		}
	}
}
