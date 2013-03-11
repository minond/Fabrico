<?php

namespace Fabrico\Core;

use Fabrico\Request\Request;
use Fabrico\Response\Response;

/**
 * base Fabrico application class. stores the request and reposnse object along
 * with basic information about the active project.
 */
class Application {
	/**
	 * project root directory
	 * @var string
	 */
	private $root;

	/**
	 * current request
	 * @var Request
	 */
	private $request;

	/**
	 * response we're sending back
	 * @var Response
	 */
	private $response;

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
	public function getRoot() {
		return $this->root;
	}

	/**
	 * request setter
	 * @param Request $req
	 */
	public function setRequest(Request & $req) {
		$this->request = $req;
	}

	/**
	 * request getter
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * response setter
	 * @param Response $res
	 */
	public function setResponse(Response & $res) {
		$this->response = $res;
	}

	/**
	 * response getter
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}
}
