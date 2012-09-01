<?php

/**
 * request parameter helper
 */
class param {
	public static function __callStatic ($method, $args) {
		return Fabrico\Router::req($method);
	}
}

/**
 * prints out object and kills the script
 *
 * @param mixed data
 */
function dpre ($data) {
	call_user_func_array('dpr', func_get_args());
	die;
}

/**
 * prints out object
 *
 * @param mixed data
 */
function dpr ($data) {
	for ($i = 0; $i < func_num_args(); $i++) {
		echo \Fabrico\html::pre([
			'content' => print_r(func_get_arg($i), true)
		]);
	}
}
