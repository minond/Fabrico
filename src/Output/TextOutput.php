<?php

namespace Fabrico\Output;

/**
 * handles text output
 */
class TextOutput implements Output {
	private $content;

	/**
	 * content setter
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * content getter
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * handles output data
	 * @return void
	 */
	public function output();
}
