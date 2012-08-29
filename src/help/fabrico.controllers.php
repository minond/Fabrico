<?php

/**
 * Fabrico controller helpers
 */

namespace Fabrico;

/**
 * data controller interface
 */
interface DataRequestController {
	/**
	 * called before data specific getter method
	 */
	public function ondata ($type);
}

/**
 * javascript method api controller
 */
interface PublicMethodController {
	/**
	 * called before requested method
	 */
	public function onmethod ($method, & $arguments);
}
