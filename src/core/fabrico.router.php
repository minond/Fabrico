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
	const R401 = '401';
	const R404 = '404';
	const VIEW = 'VIEW';
	const PDF = 'PDF';
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
		'401' => '401 Unauthorized',
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
		'callback' => '_cb',
		'session_id' => '_session_id'
	];

	/**
	 * runs custom project uri checkers and updaters
	 */
	public static function check_project_routing () {
		$ext = self::data_method();

		if ($ext) {
			$ext = strtolower(".{$ext}");
		}

		foreach (Core::$configuration->routing->placeholders as $placeholder) {
			$rawfields = Merge::get_merge_fields($placeholder);
			$fields = Merge::get_merge_fields($placeholder, true);

			// convert the placeholder string into a regular expression string
			$regexp = Merge::placeholder($placeholder, function ($field) { return '(\w+?)'; });
			$regexp = str_replace('/', '\/', $regexp);
			$regexp = "/^{$regexp}$/";

			preg_match($regexp, Project::get_file_no_data(Project::$file), $matches);

			// check if this is a matching uri
			if (count($matches)) {
				// update the request variables
				foreach ($fields as $index => $field) {
					self::$req[ $field ] = $matches[ $index + 1 ];
				}

				// and create the new uri
				$parts = explode('/', $placeholder);
				foreach ($parts as $index => $part) {
					if (in_array($part, $rawfields)) {
						unset($parts[ $index ]);
					}
				}

				// save it
				Project::$file = implode('/', $parts) . $ext;
				break;
			}
		}
	}

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
		else if (util::ends_with($req, strtolower(self::PDF))) {
			return self::PDF;
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
		$R404 = false;
		$is_pdf = false;

		// check authentication before
		if ($_controller instanceof \Fabrico\Authentication\Basic) {
			if (!$_controller->authenticate_basic()) {
				// check if we should redirect to something like a login page
				$redirect = $_controller->authenticate_redirect();

				if (self::request_method() === self::VIEW) {
					if (is_string($redirect)) {
						if ($redirect !== '/' . Core::$configuration->state->uri) {
							header("Location: {$redirect}");
							die;
						}
					}
					else {
						self::http_header(self::R401);
						require \view\template('redirect/401');
						die;
					}
				}
				else {
					die(new Response(Response::AUTHENTICATION_INVALID));
				}
			}
		}

		// then serve the requested data
		switch (self::request_method()) {
			case self::PDF:
				if (in_array(strtolower(self::PDF), $_controller->formats)) {
					$is_pdf = true;
					self::$req[ self::$uri->file ] = Project::get_file_no_data(Core::$configuration->state->uri);
					Project::set_files(false);
				}
				else {
					$R404 = true;
					break;
				}

			case self::VIEW:
				$_controller->initialize();
				$_controller->onview();

				if ($_build) {
					Page::build();
				}

				require Core::$configuration->state->build;
				
				if ($_build) {
					if ($is_pdf) {
						require_once Project::get_dependency_file('dompdf/dompdf_config.inc.php');

						$dompdf = new \DOMPDF();
						$dompdf->load_html(Page::close(false, !$is_pdf));
						$dompdf->set_paper('letter', 'portrait');
						$dompdf->render();

						$dompdf->stream(Core::$configuration->state->uri . '.pdf', [ 'Attachment' => false ]); 
					}
					else {
						echo Page::close();
					}
				}

				break;

			case self::JS:
			case self::JSON:
			case self::XML:
				if ($_controller instanceof \Fabrico\Controller\DataRequest && 
					in_array(strtolower(self::request_method()), $_controller->formats)) {
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
								$data = "{$callback}($data);";
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
				else {
					$R404 = true;
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

				// check the session before anything else
				if (self::req(self::$uri->session_id) !== $_controller->session_id()) {
					$res->status = Response::INVALID_SESSION;
					die($res);
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
					if (!($_controller instanceof \Fabrico\Controller\PublicAccess)) {
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
						$_controller->onbeforemethod($method, $arguments);

						// call the method
						try {
							$res->response = call_user_func_array([ $_controller, $method ], $arguments);
							$_controller->onaftermethod($method, $arguments);
							$res->status = Response::SUCCESS;
						} catch (\Exception $error) {
							$res->response = $error;
							$res->status = Response::ERROR;
						}
					}
				}

				if ($update && is_array($update)) {
					if (!$method) {
						// on method
						$_controller->onbeforemethod($method, $arguments);
						$res->status = Response::SUCCESS;
					}

					$res->response = call_user_func(
						[ $_controller, Controller::GET_NODE_CONTENT ], $update
					);

					if (!$method) {
						$_controller->onaftermethod($method, $arguments);
					}
				}

				self::type_header(Router::JSON);
				echo $res;
				break;

			case self::R404:
			default:
				$R404 = true;
				break;
		}

		if ($R404) {
			if (Std::controller_has_view_method($_controller)) {
				$_controller->{ Std::get_controller_view_method() }();
			}
			else {
				self::http_header(self::R404);
				require \view\template('redirect/404');
			}
		}
	}
}

Router::$uri = (object) Router::$uri;
