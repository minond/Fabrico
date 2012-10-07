<?php

namespace fabrico;

class Router {
	/**
	 * @var array
	 */
	private $request;

	/**
	 * @param array $req
	 */
	public function set_request (& $req) {
		$this->request = & $req;
	}

	/**
	 * request variable getter
	 * @param string $var
	 * @return string
	 */
	public function get ($var) {
		return isset($this->request[ $var ]) ? $this->request[ $var ] : '';
	}

	/**
	 * request variable setter
	 * @param string $var
	 * @param string $val
	 * @return string
	 */
	public function set ($var, $val) {
		return $this->request[ $var ] = $val;
	}
}
