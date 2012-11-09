<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\core\util;
use fabrico\page\OutputContent;
use fabrico\page\Page;

/**
 * response class
 */
class Response extends Module {
	/**
	 * response types
	 */
	const HTML = 'html';
	const TEXT = 'text';

	/**
	 * possible http statuses
	 */
	const HTTP404 = 'HTTP/1.0 404 Not Found';

	/**
	 * headers to send
	 * @var array
	 */
	private $headers = [];

	/**
	 * header aliases
	 * @var array
	 */
	public static $header_alias = [
		'html' => 'text/html',
		'txt' => 'text'
	];

	/**
	 * content type header
	 */
	public static $content_type = 'Content-type: %s';

	/**
	 * response type
	 * @var string
	 */
	public $as = self::HTML;

	/**
	 * @var OutputContent
	 */
	public $outputcontent;

	/**
	 * add a header
	 * @param string $header
	 */
	public function addheader ($header) {
		$this->headers[] = $header;
	}

	/**
	 * handles all reply types
	 */
	public function reply () {
		// repond in the requested format
		$this->as = $this->core->request->format;

		// send the content type
		header(sprintf(self::$content_type, self::$header_alias[ $this->as ]));

		// then the rest of the headers
		foreach ($this->headers as $header) {
			header($header);
		}

		// then the content
		switch (true) {
			case $this->outputcontent instanceof OutputContent:
				echo $this->outputcontent->render($this->as);
				break;
		}
	}
}
