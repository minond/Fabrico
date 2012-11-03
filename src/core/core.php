<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * main
 */
class core {
	/**
	 * logic loaders
	 */
	const VIEW_INITIALIZER = 'view.php';
	const CORE_INITIALIZER = 'core.php';
	const DEPS_INITIALIZER = 'deps.php';

	/**
	 * @var core
	 */
	private static $instance;

	/**
	 * @var Controller
	 */
	public $controller;

	/**
	 * @var Response
	 */
	public $response;

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
	 * @var Request
	 */
	public $request;

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
	 * @return core
	 */
	public static function instance () {
		return self::$instance = (
			self::$instance ?
			self::$instance :
			new self
		);
	}
}
