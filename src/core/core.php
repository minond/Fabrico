<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

require 'core/module.php';
require 'core/util.php';
require 'loader/loader.php';
require 'loader/coreloader.php';

/**
 * main
 * this is a singleton, but SHOULD only be accessed through
 * its run method, or through either the Mediator or Module
 */
class Core {
	/**
	 * events bound before an event manager is created.
	 * @var array
	 */
	private $event_backlog = [];
	/**
	 * @var core
	 */
	private static $instance;

	/**
	 * @var Controller
	 */
	private $controller;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var CoreLoader
	 */
	private $loader;

	/**
	 * @var DepsLoader
	 */
	private $deps;

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var EventDispatch
	 */
	private $event;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var Project
	 */
	private $project;

	/**
	 * @var Logz
	 */
	private $log;

	/**
	 * custom set function
	 * @var callable
	 */
	private $setter;

	/**
	 * custom get function
	 * @var callable
	 */
	private $getter;

	/**
	 * working directory
	 * @var string
	 */
	public $wd;

	/**
	 * custom setter setter
	 * @param callable $fn
	 */
	public function set (callable $fn) {
		$this->setter = \Closure::bind($fn, $this, get_class($this));
	}

	/**
	 * custom setter setter
	 * @param callable $fn
	 */
	public static function sets (callable $fn) {
		self::instance()->set($fn);
	}

	/**
	 * custom getter setter
	 * @param callable $fn
	 */
	public function get (callable $fn) {
		$this->getter = \Closure::bind($fn, $this, get_class($this));
	}

	/**
	 * custom getter setter
	 * @param callable $fn
	 */
	public static function gets (callable $fn) {
		self::instance()->get($fn);
	}

	/**
	 * sets anything by default
	 * @param string $prop
	 * @param mixed $val
	 */
	public function __set ($prop, $val) {
		if (is_callable($this->setter)) {
			call_user_func_array($this->setter, [ $prop, $val ]);
		}
		else {
			$this->{ $prop } = $val;
		}
	}

	/**
	 * gets anything by default
	 * @param string $prop
	 * @return mixed
	 */
	public function & __get ($prop) {
		if (is_callable($this->getter)) {
			$prop = call_user_func_array($this->getter, [ $prop ]);
		}
		else {
			$prop = & $this->{ $prop };
		}

		return $prop;
	}

	/**
	 * instance getter
	 * @return core
	 */
	public static function & instance () {
		if (!self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * initialize core and triggers the callback
	 * @param Closure $cb
	 */
	public static function run (\Closure $cb) {
		$cb(self::instance());
	}

	/**
	 * @param string $namespace
	 */
	public static function load($namespace) {
		if (isset(self::$instance->loader)) {
			self::$instance->loader->load($namespace);
		}
	}

	/**
	 * invokes Logs
	 * @param string $msg
	 * @param int $level
	 */
	public function log($msg, $level = \fabrico\logging\Logz::INFO) {
		if (isset($this->log)) {
			$this->log->log($msg, $level);
		}
	}

	/**
	 * invokation manager to EventDispatch::bind
	 * @see EventDispatch::bind
	 * @param strimg $namespace
	 * @param string $event
	 * @param callable $action
	 * @return mixed boolean|null|string
	 * return information: string - event was bound, boolean(false) - event
	 * was not bound, null - event was added to event backlog
	 */
	public function bind($namespace, $event, $action, $backlog = false) {
		if (isset($this->event)) {
			$this->event->bind($namespace, $event, $action);
		}
		else if ($backlog) {
			$this->event_backlog[] = func_get_args();
		}
	}

	// public func
}
