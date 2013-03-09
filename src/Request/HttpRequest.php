<?php

namespace Fabrico\Request;

/**
 * represents a request coming via http
 */
class HttpRequest implements ApplicationRequest {
	/**
	 * http request parameters
	 * @var array
	 */
	private $request_data;

	/**
	 * requested view file
	 * @var string
	 */
	private $view_file;

	/**
	 * @return boolean
	 */
	public function valid() {
		return !!$this->view_file;
	}

	/**
	 * @return boolean
	 */
	public function load() {}

	/**
	 * request data setter
	 * @param array $data
	 */
	public function setRequestData(& $data) {
		$this->data = $data;
	}

	/**
	 * request data getter
	 * @return array
	 */
	public function getRequestData(& $data) {
		return $this->request_data;
	}

	/**
	 * view file setter
	 * @param string $fiel
	 */
	public function setViewFile($file) {
		$this->view_file = $file;
	}

	/**
	 * view file getter
	 * @return string
	 */
	public function getViewFile() {
		return $this->view_file;
	}
}
