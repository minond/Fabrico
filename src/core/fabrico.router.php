<?php

namespace Fabrico;

class Router {
	/**
	 * HTTP methods
	 */
	const POST = 'POST';
	const GET = 'GET';

	/**
	 * Fabrico methods
	 */
	const VIEW = 'VIEW';
	const METHOD = 'METHOD';
	const ERROR = 'ERROR';

	/**
	 * request variable
	 *
	 * @var array
	 */
	private static $req = array();

	/**
	 * standard uri variables
	 *
	 * @var object
	 */
	public static $uri = array(
		'id' => 'id',
		'file' => '_file',
		'args' => '_args',
		'method' => '_method',
		'debug' => '_debug',
		'success' => '_success',
		'failure' => '_failure',
		'error' => '_error'
	);

	/**
	 * setter for request variable
	 *
	 * @param array
	 */
	public static function set_request (& $req) {
		self::$req = & $req;
	}

	/**
	 * returns the requested file name
	 *
	 * @param clean name
	 * @return string
	 */
	public static function get_file_requested ($clean = true) {
		$file = self::req(self::$uri->file);

		if ($clean) {
			$parts = array_filter(explode('/', $file), function ($part) {
				return $part !== '';
			});

			$file = implode('/', $parts);
		}

		return $file;
	}

	/**
	 * returns current HTTP requests method type
	 *
	 * @return string
	 */
	public static function request_method_http () {
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * returns current request method type
	 *
	 * @return string
	 */
	public static function request_method () {
		$type = false;

		if (isset(self::$req[ self::$uri->file ])) {
			if (self::request_method_http() === self::GET) {
				if (isset(self::$req[ self::$uri->error ])) {
					$type = self::ERROR;
				}
				else {
					$type = self::VIEW;
				}
			}
			else if (isset(self::$req[ self::$uri->method ]) &&
				self::request_method_http() === self::POST) {
					$type = self::METHOD;
			}
		}

		return $type;
	}

	/**
	 * returns a url parameter from the request object
	 *
	 * @param string url parameter name
	 * @return string url parameter value
	 */
	public static function req ($name) {
		return array_key_exists($name, self::$req) ?
		       self::$req[ $name ] : '';
	}
}

Router::$uri = (object) Router::$uri;
