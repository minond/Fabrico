<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * main
 */
class Core {
	/**
	 * @var Core
	 */
	private static $instance;

	/**
	 * @var CoreLoader
	 */
	public $core;

	/**
	 * @var DepsLoader
	 */
	public $deps;

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
