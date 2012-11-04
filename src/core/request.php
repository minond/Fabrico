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
	public $file;

	/**
	 * requested format
	 * defaults to HTML
	 * @var string
	 */
	public $format = Response::HTML;

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
