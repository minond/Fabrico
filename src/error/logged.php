<?php

/**
 * @package fabrico\error
 */
namespace fabrico\error;

/**
 * exception logger
 */
class LoggedException extends \Exception {
	public function __construct ($message, $code = 0, \Exception $previous = null) {
		// log error
		parent::__construct($message, $code, $previous);
	}
}
