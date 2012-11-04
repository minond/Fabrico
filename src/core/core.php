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
	private $core;

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
	 * @var Reader
	 */
	private $reader;

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
	 * custom setter setter
	 * @param callable $fn
	 */
	public function set (callable $fn) {
		$this->setter = \Closure::bind($fn, $this, get_class($this));
	}

	/**
	 * custom getter setter
	 * @param callable $fn
	 */
	public function get (callable $fn) {
		$this->getter = \Closure::bind($fn, $this, get_class($this));
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
}
