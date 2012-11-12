<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\core\Response;

/**
 * request object
 */
class Request {
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
		$this->file = $file;
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
			$this->file = implode('.', $parts);
		}
		else {
			$this->file = $file;
		}
	}
}