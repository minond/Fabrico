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
	 * @var Reader
	 */
	public $reader;

	/**
	 * @var EventDispatch
	 */
	public $event;

	/**
	 * @var Configuration
	 */
	public $configuration;

	/**
	 * @var Project
	 */
	public $project;

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
