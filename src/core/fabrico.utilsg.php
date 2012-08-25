<?php

/**
 * request parameter helper
 */
class param {
	public static function __callStatic ($method, $args) {
		return Fabrico\Router::req($method);
	}
}
