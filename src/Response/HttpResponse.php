<?php

namespace Fabrico\Response;

use Fabrico\Output\Output;
use Fabrico\Output\HttpOutput;

/**
 * responds to a browser
 */
class HttpResponse implements Response {
	/**
	 * @var Output
	 */
	private $output;

	/**
	 * headers
	 * @var array
	 */
	private $headers = [];

	/**
	 * output setter
	 * @param Output $output
	 */
	public function setOutput(Output $output) {
		$this->output = $output;

		if ($output instanceof HttpOutput) {
			$this->setHeaders($output->getHeaders());
		}
	}

	/**
	 * output getter
	 * @return Output
	 */
	public function getOutput() {
		return $this->output;
	}

	/**
	 * add a header
	 * @param string $header
	 * @param string $value
	 * @param boolean $overwrite
	 */
	public function setHeader($header, $value, $overwrite = false) {
		if ($overwrite || !$this->hasHeader($header)) {
			$this->headers[ $header ] = $value;
		}
	}

	/**
	 * sets multiple headers at once
	 * @param array $headers
	 * @param boolean $overwrite
	 */
	public function setHeaders($headers, $overwrite = false) {
		foreach ($headers as $header => $value) {
			$this->setHeader($header, $value, $overwrite);
		}
	}

	/**
	 * get a header's value
	 * @param string $header
	 * @return string
	 */
	public function getHeader($header) {
		return $this->hasHeader($header) ?
			$this->headers[ $header ] : null;
	}

	/**
	 * checks if a header has been test
	 * @param string $header
	 * @return boolean
	 */
	public function hasHeader($header) {
		return array_key_exists($header, $this->headers);
	}

	/**
	 * removes a header
	 * @param string $header
	 */
	public function removeHeader($header) {
		unset($this->headers[ $header ]);
	}

	/**
	 * @return boolean
	 */
	public function ready() {
		return isset($this->output);
	}

	/**
	 * @return mixed
	 */
	public function send() {
		// send headers
		// then output
		$this->output->output();
	}
}
