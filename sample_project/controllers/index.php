<?php

class Index extends FabricoController {
	public function __construct () {
		$this->allow('test', 'check');
		$this->register('test', 'config');

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
}
