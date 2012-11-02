<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\page\Page;

/**
 * response class
 */
class Response extends Module {
	/**
	 * @var Page
	 */
	public $page;

	public function reply () {
		if ($this->page instanceof Page) {
			echo $this->page->render();
		}
	}
}
