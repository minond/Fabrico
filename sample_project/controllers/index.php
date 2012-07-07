<?php

class Index extends MainController {
	public function __construct () {
		parent::__construct();
		$this->allow('test', 'check', 'filelog');
		$this->register('test', 'config', 'adduser');

		$this->posts = range(1, 20);

	}

	public $posts;
	public $name = 'aaaaaaaaaaaaaa';

	public function test ($a, $b) {
		global $name;
		$name = 'dsssssssssssssssss';

		$ret = new stdClass;
		$ret->a = $a;
		$ret->b = $b;
		$ret->n = $this->name;
		$ret->c = true;
		$ret->x = $this->action('test', array('d','s'));
		$ret->y = $this->action('check');

		return $ret;
		return array(
			'a' => $a,
			'b' => $b,
			'n' => $this->name
		);
	}

	public function config () {
		$ret = new stdClass;

		$ret->config = Fabrico::get_config();
		$ret->directories = Fabrico::$directory;
		$ret->file = Fabrico::$file;
		$ret->controller = Fabrico::$controller;

		return $ret;
	}

	public function adduser () {
		$a = new User;
	}
}



class User extends FabricoModel {
	protected $table = 'users';

	public function __construct () {
		parent::__construct();
	}   
}













