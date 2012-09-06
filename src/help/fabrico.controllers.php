<?php

/**
 * Fabrico controller helpers
 */

namespace Fabrico\Controller;

/**
 * data controller interface
 */
interface DataRequest {
	/**
	 * called before data specific getter method
	 */
	public function ondata ($type);
}

/**
 * javascript method api controller
 */
interface PublicAccess {
	/**
	 * called after requested method
	 */

	public function onaftermethod ($method, & $arguments);
	/**
	 * called before requested method
	 */
	public function onbeforemethod ($method, & $arguments);
}
