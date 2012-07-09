<?php

class Index extends MainController {
	public $user;
	public $user_id;
	public $user_set = false;
	public $logged_in = false;

	public function __construct () {
		parent::__construct();
		$this->uses('User');
		$this->register('get_user_info');
		$this->user_id = self::req('id');

		if (Fabrico::is_view_request()) {
			$this->get_user_info();
		}
	}

	private function check_login () {
		$this->logged_in = false === false;

		return $this->logged_in;
	}

	public function get_user_info ($id = false) {
		$id = $id ? $id : $this->user_id;

		if (!$this->logged_in) {
			$this->check_login();
		}

		if ($this->logged_in && !isset($this->user) && $id) {
			$this->user = User::get($id);
			$this->user_set = isset($this->user);
		}

		return $this->user;
	}
}
