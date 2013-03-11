<?php

namespace Fabrico\Output;

/**
 * handles text output
 */
class TextOutput implements Output, HttpOutput {
	protected $content;

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
	public function output() {
		echo $this->content;
	}

	/**
	 * sets Content-Type to text
	 * @codeCoverageIgnore
	 */
	public function getHeaders() {
		return [
			'Content-Type' => 'text'
		];
	}
}
