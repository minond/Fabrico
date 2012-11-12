<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\core\util;
use fabrico\core\Module;

/**
 * request file server
 */
class Router extends Module {
	/**
	 * standard request variable names
	 * @var object
	 */
	public static $var;

	/**
	 * @var array
	 */
	private $request;

	/**
	 * true if current request is for a view
	 * @var boolean
	 */
	public $is_view = false;

	/**
	 * true if current request is for an element update
	 * @var boolean
	 */
	public $is_update = false;

	/**
	 * true if current request is for a controller method call
	 * @var boolean
	 */
	public $is_method = false;

	/**
	 * @param array $req
	 */
	public function __construct (& $req) {
		$this->request = & $req;
		$this->parse_request_type();
		$this->build_request();
	}

	/**
	 * figures out the request type
	 */
	public function parse_request_type () {
		if ($this->get(self::$var->file)) {
			if ($this->get(self::$var->method) && $this->get(self::$var->controller)) {
				$this->is_method = true;
			}
			else if ($this->get(self::$var->element)) {
				$this->is_update = true;
			}
			else {
				$this->is_view = true;
			}
		}
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

	/**
	 * parses a raw request uri
	 * @param string $raw
	 * @return
	 */
	public function parse_raw_request_uri ($raw) {
		return $raw;
	}

	/**
	 * @return Request
	 */
	public function build_request () {
		$req = & $this->core->request;
		$req->parse($this->get(self::$var->file));
		return $req;
	}
}

// set vars
Router::$var = (object) [
	// file requests
	'file' => '_file',
	// controller name
	'controller' => '_controller',
	// controller method calls
	'method' => '_method',
	// controller method call arguments
	'args' => '_args',
	// controller variables
	'env' => '_env',
	// component updates
	'element' => '_element'
];
