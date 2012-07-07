<?php

class State {
	public static function load ($state) {}

	public static function save ($as_string = false) {
		$state = new stdClass;
		$state->config = Fabrico::get_config();
		$state->file = Fabrico::$file;
		$state->file_path = Fabrico::get_requested_file();
		$state->controller = Fabrico::$controller;
		$state->controller_path = Fabrico::$controller;

		return $as_string ? serialize($state) : $state;
	}
}
