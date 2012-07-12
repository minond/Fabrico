<?php

class MainController extends FabricoController {
	// cookies
	const USER_ID = 'user_id';
	const LOGGED_IN = 'logged_in';

	// user information
	public $user;
	public $user_id;
	public $user_set = false;

	// login information
	public $logged_in;
	public $login_invalid = false;

	public function __construct () {
		parent::__construct();
		$this->uses('User');
		$this->allow('filelog');
		$this->register('login', 'logout');

		$this->check_login();
		$this->get_user_id();

		if (Fabrico::is_view_request()) {
			$this->get_user_info();
			$this->login_invalid = Fabrico::is_invalid('password');
		}
	}

	private function get_user_id () {
		return isset($this->user_id) ? $this->user_id :
		       $this->user_id = Fabrico::get_cookie(self::USER_ID);
	}

	private function check_login () {
		return isset($this->logged_in) ? $this->logged_in :
		       $this->logged_in = Fabrico::get_cookie(self::LOGGED_IN);
	}

	public function get_user_info ($id = false) {
		$id = $id ? $id : $this->user_id;

		if ($this->logged_in && !isset($this->user) && $id) {
			$this->user = User::get($id);
			$this->user_set = isset($this->user);
		}

		return $this->user;
	}

	public function logout () {
		Fabrico::set_cookie(self::LOGGED_IN, 0);
		Fabrico::set_cookie(self::USER_ID, 0);
		Fabrico::handle_success();
	}

	public function login () {
		$user = User::check();

		if ($user->id) {
			Fabrico::set_cookie(self::LOGGED_IN, true);
			Fabrico::set_cookie(self::USER_ID, $user->id);
			Fabrico::handle_success();
		}
		else {
			Fabrico::handle_failure(array(
				Fabrico::$uri_query_invalid => 'password',
				'email' => Fabrico::req('email')
			));
		}

		return $user;
	}
}
