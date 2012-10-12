<?php

namespace fabrico;

class Router {
	/**
	 * @var array
	 */
	private $request;

	/**
	 * standard request variable names
	 * @var object
	 */
	public static $var;

	/**
	 * @param array $req
	 */
	public function request (& $req) {
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

	public function route () {
		util::dpr($this->request, self::$var);
	}
}

// set vars
Router::$var = (object) [
	// file requests
	'file' => '_file',
	// controller method calls
	'method' => '_method',
	// controller method call arguments
	'args' => '_args',
	// controller variables
	'env' => '_env',
	// component updates
	'element' => '_element'
];
