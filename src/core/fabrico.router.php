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
	const UPDATE = 'UPDATE';
	const ERROR = 'ERROR';
	const JSON = 'JSON';
	const JS = 'JS';
	const XML = 'XML';

	/**
	 * type headers
	 * @var array
	 */
	private static $headers = [
		'404' => '404 Not Found',
		'json' => 'application/json',
		'js' => 'application/javascript',
		'xml' => 'text/xml'
	];

	/**
	 * request variable
	 * @var array
	 */
	private static $req = [];

	/**
	 * standard uri variables
	 * @var object
	 */
	public static $uri = [
		'id' => 'id',
		'file' => '_file',
		'args' => '_args',
		'env' => '_env',
		'method' => '_method',
		'update' => '_update',
		'debug' => '_debug',
		'success' => '_success',
		'failure' => '_failure',
		'error' => '_error',
		'callback' => '_cb'
	];

	/**
	 * setter for request variable
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
	 * returns current HTTP requests url
	 *
	 * @return string
	 */
	public static function request_method_uri () {
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * returns current HTTP requests query
	 *
	 * @return string
	 */
	public static function request_query () {
		return explode('?', self::request_method_uri())[ 0 ];
	}

	/**
	 * returns the requested method type
	 *
	 * @return string data type
	 */
	private static function data_method () {
		$req = self::request_query();

		if (util::ends_with($req, strtolower(self::JSON))) {
			return self::JSON;
		}
		else if (util::ends_with($req, strtolower(self::XML))) {
			return self::XML;
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
				else if (self::data_method()) {
					$type = self::data_method();
				}
				else {
					$type = self::R404;
				}
			}
			else if (isset(self::$req[ self::$uri->update ]) &&
				self::request_method_http() === self::POST) {
					$type = self::UPDATE;
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

	/**
	 * handles a page request
	 *
	 * @param object controller instace
	 * @param string view file to use
	 * @param boolean build view file
	 */
	public static function handle_request (& $_controller, $_view, $_build = false) {
		switch (self::request_method()) {
			case self::VIEW:
				$_controller->initialize();
				$_controller->onview();

				if ($_build) {
					Page::build();
				}
				
				require Core::$configuration->state->build;
				
				if ($_build) {
					echo Page::close();
				}
				
				break;

			case self::JS:
			case self::JSON:
			case self::XML:
				if ($_controller instanceof \Fabrico\DataRequestController) {
					switch (self::request_method()) {
						case self::JS:
						case self::JSON:
							self::type_header(Router::JSON);
							$_controller->initialize();
							$data = json_encode(
								$_controller->ondata(self::request_method())
							);

							if (self::request_method() === self::JS) {
								$callback = self::req(self::$uri->callback);
								$data = "{$callback}($data)";
							}

							echo $data;

							break;

						case self::XML:
							self::type_header(Router::XML);
							echo \DOM::arrayToXMLString(
								$_controller->ondata(self::request_method()), 'root', true
							);

							break;
					}
				}

				break;

			case self::UPDATE:
			case self::METHOD:
				$res = new Response(Response::IN_PROCESS);
				$arguments = self::req(self::$uri->args);
				$envirment = self::req(self::$uri->env);

				// request types
				$method = self::req(self::$uri->method);
				$update = self::req(self::$uri->update);

				if (!$arguments) {
					$arguments = [];
				}

				if (!$envirment) {
					$envirment = [];
				}

				// env setter
				foreach ($envirment as $field => $value) {
					if (!property_exists($_controller, $field)) {
						$res->status = Response::METHOD_UNKNOWN_VARIABLE;
						die($res);
					}
					else if (!in_array($field, $_controller->public)) {
						$res->status = Response::METHOD_PRIVATE_VARIABLE;
						die($res);
					}
					else {
						$_controller->{ $field } = $value;
					}
				}

				$_controller->initialize();

				if ($method) {
					// check if controller allows method requests
					if (!($_controller instanceof \Fabrico\PublicMethodController)) {
						$res->status = Response::METHOD_PRIVATE_CLASS;
					}
					// check if method exits
					else if (!method_exists($_controller, $method)) {
						$res->status = Response::METHOD_UNKNOWN_METHOD;
					}
					// check if method is public
					else if (!in_array($method, $_controller->public)) {
						$res->status = Response::METHOD_PRIVATE_METHOD;
					}
					else {
						// on method
						$_controller->onmethod($method, $arguments);

						// call the method
						$res->status = Response::SUCCESS;
						$res->response = call_user_func_array(
							[ $_controller, $method ], $arguments
						);
					}
				}

				if ($update && is_array($update)) {
					if (!$method) {
						// on method
						$_controller->onmethod($method, $arguments);
						$res->status = Response::SUCCESS;
					}

					$res->response = call_user_func(
						[ $_controller, Controller::GET_NODE_CONTENT ], $update
					);
				}

				self::type_header(Router::JSON);
				echo $res;

				break;

			case self::R404:
			default:
				self::http_header(self::R404);
				require \view\template('redirect/404');

				break;
		}
	}
}

Router::$uri = (object) Router::$uri;
