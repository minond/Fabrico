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
	const METHOD_PRIVATE_CLASS = 'private_class';
	const METHOD_UNKNOWN_METHOD = 'unknown_method';
	const METHOD_PRIVATE_METHOD = 'private_method';
	const METHOD_UNKNOWN_VARIABLE = 'unknown_variable';
	const METHOD_PRIVATE_VARIABLE = 'private_variable';

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
		Logger::request("response: {$this->status}");
		return json_encode($this);
	}
}
