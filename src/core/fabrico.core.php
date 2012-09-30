<?php

namespace fabrico;

class Core {
	/**
	 * @var Core
	 */
	private static $instance;

	/**
	 * @var Loader
	 */
	public $loader;

	/**
	 * @var Router
	 */
	public $router;

	/**
	 * instance getter
	 * @return Core
	 */
	public static function instance () {
		return self::$instance = (
			self::$instance ?
			self::$instance :
			new self
		);
	}
}
