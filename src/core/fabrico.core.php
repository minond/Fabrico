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
	 * @var EventDispatch
	 */
	public $event;

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
