<?php

namespace Fabrico\Event;

/**
 * holds information about a subscription to an observable object
 */
class Listener {
	/**
	 * before a function call
	 */
	const PRE = 'pre';

	/**
	 * after a function call
	 */
	const POST = 'post';

	/**
	 * listener name
	 * @var string
	 */
	private $name;

	/**
	 * listener type
	 * @var string
	 */
	private $type;

	/**
	 * listener handler
	 * @var mixed callable|Closure
	 */
	private $func;

	/**
	 * @param string $name
	 * @param string $type
	 * @param mixed callable|Closure $func
	 */
	public function __construct($name, $type, $func) {
		$this->name = $name;
		$this->type = $type;
		$this->func = $func;
	}

	/**
	 * helper function for checking if listener should be triggered
	 * @param string $name
	 * @param string $type
	 * @return boolean
	 */
	public function is($name, $type) {
		return $this->type === $type && $this->name === $name;
	}

	/**
	 * trigger this listener
	 * @param array $args
	 */
	public function trigger(array $args = array()) {
		return call_user_func_array($this->func, $args);
	}
}
