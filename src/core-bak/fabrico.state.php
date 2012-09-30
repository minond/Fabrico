<?php

namespace Fabrico;

class State {
	/**
	 * session storage root
	 */
	const ROOT = '__state';

	/**
	 * initializes session storage
	 */
	private static function initialize () {
		if (!isset($_SESSION[ self::ROOT ])) {
			$_SESSION[ self::ROOT ] = [];
		}
	}

	/**
	 * clears session storage
	 *
	 * @param string optional class name
	 */
	private static function clear ($klass = '') {
		if ($klass) {
			$_SESSION[ self::ROOT ][ $klass ] = [];
		}
		else {
			$_SESSION[ self::ROOT ] = [];
		}
	}

	/**
	 * saves the state from a controller instance
	 *
	 * @param Controller instance
	 */
	public static function save (& $controller) {
		self::initialize();
		$_SESSION[ self::ROOT ][ get_class($controller) ] = $controller->__get_state();
	}

	/**
	 * loads state onto a controller instance
	 *
	 * @param Controller instance
	 */
	public static function load (& $controller) {
		self::initialize();

		if (isset($_SESSION[ self::ROOT ][ get_class($controller) ])) {
			$controller->__load_state(
				$_SESSION[ self::ROOT ][ get_class($controller) ]
			);
		}
	}
}
