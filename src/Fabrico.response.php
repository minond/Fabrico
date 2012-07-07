<?php

class FabricoResponse {
	private $return;

	/**
	 * @name FabricoResponse
	 * @param string action status
	 * @param mixed action result
	 */
	public function __construct ($status, $response = '') {
		$this->return = new stdClass;
		$this->return->status = $status;
		$this->return->response = $response;
	}

	/**
	 * @name out
	 * @return stdClass response object
	 */
	public function get () {
		return $this->return;
	}

	/**
	 * @name out
	 * @return void
	 * outputs a json encoded response and ends the program
	 */
	public function out () {
		die(json_encode($this->return));
	}
}


/**
 * @name FabricoError
 * @param string error title
 * @param string error message
 * @param string file name
 * @param int line number
 * @return stdClass error object
 */
function FabricoError ($title, $message, $filename, $line) {
	$error = new stdClass;

	$error->title = $title;
	$error->message = $message;
	$error->filename = $filename;
	$error->line = $line;

	util::log(Fabrico::ERROR, $error);
	return $error;
}
