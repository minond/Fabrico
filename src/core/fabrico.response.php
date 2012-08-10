<?php

namespace Fabrico;

class Response {
	/**
	 * statuses
	 */
	const ERROR = 'error';
	const SUCCESS = 'success';
	const IN_PROCESS = 'in_process';

	/**
	 * method specific statuses
	 */
	const METHOD_UNKNOWN_METHOD = 'unknown_method';
	const METHOD_PRIVATE_METHOD = 'private_method';

	/**
	 * response status
	 *
	 * @var string
	 */
	public $status;

	/**
	 * response message
	 *
	 * @var response
	 */
	public $response;

	/**
	 * construcor
	 *
	 * @param string status
	 * @param string response
	 */
	public function __construct ($status = '', $response = '') {
		$this->status = $status;
		$this->response = $response;
	}

	/**
	 * overwrite to string
	 */
	public function __toString () {
		return json_encode($this);
	}
}