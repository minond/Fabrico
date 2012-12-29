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
		echo "<pre>\n";

		foreach (func_get_args() as $arg) {
			print_r($arg);
		}

		echo "\n</pre>";
	}

	/**
	 * prints arguments then kill script
	 * @param mixed $output*
	 */
	public static function dpre ($output) {
		call_user_func_array([ 'self', 'dpr' ], func_get_args());
		die;
	}

	/**
	 * simple merge field parser
	 * @param string $tmpl
	 * @param mixed $data
	 * @param string $flag
	 * @return string
	 */
	public static function merge ($tmpl, $data, $flag = '#') {
		foreach ($data as $field => $value) {
			if (is_scalar($value)) {
				$tmpl = str_replace("{$flag}{{$field}}", $value, $tmpl);
			}
		}

		return $tmpl;
	}
}
