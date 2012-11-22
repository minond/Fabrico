<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\core\Module;
use fabrico\core\Response;

/**
 * request object
 */
class Request extends Module {
	/**
	 * valid formats
	 * @var array
	 */
	public static $formats = [ 'html', 'txt', 'js', 'json' ];

	/**
	 * view file
	 * @var string
	 */
	private $file;

	/**
	 * requested format
	 * defaults to HTML
	 * @var string
	 */
	private $format = Response::HTML;

	/**
	 * format getter
	 * @param string
	 */
	public function get_format () {
		return $this->format;
	}

	/**
	 * format setter
	 * @param string $format
	 */
	public function set_format ($format) {
		$this->format = $format;
	}

	/**
	 * file fetter
	 * @return string
	 */
	public function get_file () {
		return $this->file;
	}

	/**
	 * file setter
	 * @param string $file
	 */
	public function set_file ($file) {
		$this->file = preg_replace([ '/^\//', '/\/$/' ], '', $file);
	}

	/**
	 * parse a raw request
	 */
	public function parse ($file) {
		$parts = explode('.', $file);
		$ext = array_pop($parts);

		// remove the extension and save it
		if (count($parts) && in_array($ext, self::$formats)) {
			$this->format = $ext;
			$this->set_file(implode('.', $parts));
		}
		else {
			$this->set_file($file);
		}
	}

	/**
	 * request value getter
	 * @param string $var
	 * @return mixed
	 */
	public function get ($var) {
		// kind of ugly, shoudl find better way
		// or move the request handling over to this class
		return $this->core->router->get($var);
	}
}
