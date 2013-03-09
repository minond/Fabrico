<?php

namespace Fabrico\Core;

use \Fabrico\Request\ApplicationRequest;

class Application {
	/**
	 * project root directory
	 * @var string
	 */
	private $root;

	/**
	 * current request (ie. http, cli)
	 * @var ApplicationRequest
	 */
	private $request;

	/**
	 * project root setter
	 * @param string $root
	 */
	public function setRoot($root) {
		$this->root = $root;
	}

	/**
	 * project root setter
	 * @return string
	 */
	public function getRoot($root) {
		return $this->root;
	}

	/**
	 * request setter
	 * @param ApplicationRequest $request
	 */
	public function setRequest(ApplicationRequest & $request) {
		return $this->request = $request;
	}

	/**
	 * request getter
	 * @return ApplicationRequest
	 */
	public function getRequest() {
		return $this->request;
	}
}
