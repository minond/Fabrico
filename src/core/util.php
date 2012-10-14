<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * utility belt class
 */
class util {
	/**
	 * prints arguments
	 * @param mixed $output*
	 */
	public static function dpr ($output) {
		echo '<pre>';

		foreach (func_get_args() as $arg) {
			print_r($arg);
		}

		echo '</pre>';
	}

	/**
	 * prints arguments then kill script
	 * @param mixed $output*
	 */
	public static function dpre ($output) {
		call_user_func_array([ 'self', 'dpr' ], func_get_args());
		die;
	}
}
