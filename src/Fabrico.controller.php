<?php

class FabricoController {
	/**
	 * @nane whitelabeled
	 * @var array of white labeled variables
	 */
	private $whitelabeled = array();

	/**
	 * @name allows
	 * @var array of allowed actions controller can take
	 */
	private $allows = array();

	/**
	 * @name registered
	 * @var array of registered methods
	 */
	private $registered = array();

	/**
	 * @name FabricoController
	 */
	public function __construct () {}

	/**
	 * @name register
	 * @param string* methods to register
	 * @return int number of registered methods
	 */
	final protected function register () {
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$method = func_get_arg($i);

			if (!in_array($method, $this->registered)) {
				$this->registered[] = $method;
			}
		}

		return count($this->registered);
	}

	/**
	 * @name registered
	 * @param string method in question
	 * @return bool true if method is registered
	 */
	final public function registered ($method) {
		return in_array($method, $this->registered);
	}


	/**
	 * @name whitelabel
	 * @param string* variables to whitelabel
	 * @return int number of white labeled variables
	 */
	final protected function whitelabel () {
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$var = func_get_arg($i);

			if (!in_array($var, $this->whitelabeled)) {
				$this->whitelabeled[] = $var;
			}
		}

		return count($this->whitelabeled);
	}

	/**
	 * @name whitelabeled
	 * @param string parameter in question
	 * @return bool true if variable is whitelabeled
	 */
	final public function whitelabeled ($var) {
		return in_array($var, $this->whitelabeled);
	}

	/**
	 * @name allow
	 * @param string* actions to allows
	 * @return int number of allowed actions
	 */
	final protected function allow () {
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$action = func_get_arg($i);

			if (!in_array($action, $this->allows)) {
				$this->allows[] = $action;
			}
		}

		return count($this->allows);
	}

	/**
	 * @name allows
	 * @param string action in question
	 * @return bool true if action is allowed
	 */
	final public function allows ($action) {
		return in_array($action, $this->allows);
	}

	/**
	 * @name uses
	 * @param models* to include
	 */
	final public function uses () {
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$model = func_get_arg($i);

			require_once Fabrico::get_model_file(
				strtolower($model)
			);

			$model::init();
		}
	}

	/**
	 * @name action
	 * @param string action name
	 * @param array optional arguments
	 */
	final protected function action ($action, $args = array()) {
		if ($this->allows($action)) {
			require_once Fabrico::get_action_file($action);

			return call_user_func_array(
				Fabrico::clean_action_name($action), $args
			);
		}
	}

	/**
	 * @name req
	 * @param string query parameter
	 * @return string parameter value
	 */
	final public function req ($key) {
		return Fabrico::req($key);
	}
}
