<?php

class MainController extends FabricoController {
	public function __construct () {
		parent::__construct();

		$this->allows('log');
		$this->register('login', 'logout');
	}

	public function login () {
		return 'logging in...';
	}

	public function logout () {
		return 'logging out...';
	}
}
