<?php

namespace Fabrico\Element;

class Param {
	private static $readers = [];
	private static $writers = [];

	public static function register_reader ($element_type, $callable) {
		if (array_key_exists($element_type, self::$readers)) {
			throw new \Exception("Parameter reader of type {$element_type} has already been declared!");
		}

		self::$readers[ $element_type ] = $callable;
	}

	public static function register_writer ($param_type, $callable) {
		if (array_key_exists($param_type, self::$writers)) {
			throw new \Exception("Parameter writer of type {$param_type} has already been declared!");
		}

		self::$writers[ $param_type ] = $callable;
	}

	public static function run_reader ($element_type, & $params) {
		return call_user_func(
			self::$readers[ $element_type ],
			$params
		);
	}

	public static function run_writer ($param_type, $params) {
		return call_user_func_array(
			self::$writers[ $param_type ],
			$params
		);
	}
}
