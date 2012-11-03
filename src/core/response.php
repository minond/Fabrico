<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\core\util;
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
	 * response type
	 * @var string
	 */
	public $as;

	/**
	 * @var Page
	 */
	public $page;

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
		foreach ($this->headers as $header) {
			header($header);
		}

		if ($this->page instanceof Page) {
			echo $this->page->render($this->as);
		}
	}
}
