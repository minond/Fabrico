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
	const R404 = '404';
	const VIEW = 'VIEW';
	const METHOD = 'METHOD';
	const ERROR = 'ERROR';
	const JSON = 'JSON';
	const XML = 'xml';
	const CSV = 'csv';
	const JS = 'js';

	/**
	 * type headers
	 *
	 * @var array
	 */
	private static $headers = array(
		'json' => 'application/json',
		'xml' => 'text/xml',
		'csv' => 'text',
		'404' => '404 Not Found'
	);

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
	 * returns the requested method type
	 *
	 * @return string data type
	 */
	public static function data_method () {
		$req = explode('?', $_SERVER['REQUEST_URI'])[ 0 ];

		if (util::ends_with($req, strtolower(self::JSON))) {
			return self::JSON;
		}
		else if (util::ends_with($req, strtolower(self::XML))) {
			return self::XML;
		}
		else if (util::ends_with($req, strtolower(self::CSV))) {
			return self::CSV;
		}
		else if (util::ends_with($req, strtolower(self::JS))) {
			return self::JS;
		}
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
				else if (file_exists(Core::$configuration->state->view)) {
					$type = self::VIEW;
				}
				else {
					$type = self::R404;
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
	 * sends a type header
	 *
	 * @param string type
	 */
	public static function type_header ($type) {
		$type = strtolower($type);

		if (array_key_exists($type, self::$headers)) {
			header('Content-Type: ' . self::$headers[ $type ]);
		}
	}

	/**
	 * sends a response header
	 *
	 * @param string type
	 */
	public static function http_header ($type) {
		if (array_key_exists($type, self::$headers)) {
			header('HTTP/1.0 ' . self::$headers[ $type ]);
		}
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
