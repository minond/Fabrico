<?php

namespace Fabrico\Request;

class HttpRequest implements Request {
	/**
	 * request parameters
	 * @var array
	 */
	private $data;

	/**
	 * view file requested
	 * @var string
	 */
	private $view_file;

	/**
	 * controller requested
	 * @var string
	 */
	private $controller;

	/**
	 * method requested
	 * @var string
	 */
	private $method;

	/**
	 * action requested
	 * @var string
	 */
	private $action;

	/**
	 * gives access to $data values
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var) {
		return array_key_exists($var, $this->data) ?
			$this->data[ $var ] : null;
	}

	/**
	 * gives access to $data values
	 * @param string $var
	 * @param mixed $val
	 */
	public function __set($var, $val) {
		return array_key_exists($var, $this->data) ?
			$this->data[ $var ] = $val : null;
	}

	/**
	 * view file setter
	 * @param string $file
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

	/**
	 * controller setter
	 * @param string $controller
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * controller getter
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * method setter
	 * @param string $method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * method getter
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * action setter
	 * @param string $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * action getter
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @return boolean
	 */
	public function valid() {
		// we'll require one of the following (in this order):
		$valid = false;

		// a route
		// TODO: implement routes
		if ($this->controller && $this->action) {
			// a controller action
			$valid = true;
		} else if ($this->controller && $this->method) {
			// a controller method
			$valid = true;
		} else if ($this->view_file) {
			// a view file
			$valid = true;
		}

		return $valid;
	}

	/**
	 * data setter
	 * @param array $data
	 */
	public function setData(array & $data) {
		$this->data = & $data;
	}

	/**
	 * data getter
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}
}
