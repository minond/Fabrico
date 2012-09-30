<?php

namespace fabrico;

class Router {
	/**
	 * @var array
	 */
	private $request;

	/**
	 * @param array $req
	 */
	public function set_request (& $req) {
		$this->request = & $req;
	}
}
