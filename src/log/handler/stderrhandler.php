<?php

/**
 * @package fabrico\logging\handler
 */
namespace fabrico\logging\handler;

/**
 * echo 1>&2
 */
class StdErrHandler extends LogzHandler {
	/**
	 * writes to file
	 * @param string $msg
	 */
	public function message ($msg) {
		file_put_contents('php://stderr', $msg . PHP_EOL);
	}
}
