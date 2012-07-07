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
	 * @name register
	 * @param string* methods to register
	 * @return int number of registered methods
	 */
	protected function register () {
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
	public function registered ($method) {
		return in_array($method, $this->registered);
	}


	/**
	 * @name whitelabel
	 * @param string* variables to whitelabel
	 * @return int number of white labeled variables
	 */
	protected function whitelabel () {
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
	public function whitelabeled ($var) {
		return in_array($var, $this->whitelabeled);
	}

	/**
	 * @name allow
	 * @param string* actions to allows
	 * @return int number of allowed actions
	 */
	protected function allow () {
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
	public function allows ($action) {
		return in_array($action, $this->allows);
	}

	/**
	 * @name action
	 * @param string action name
	 * @param array optional arguments
	 */
	protected function action ($action, $args = array()) {
		if ($this->allows($action)) {
			require_once Fabrico::get_action_file($action);
			return call_user_func_array($action, $args);
		}
	}
}
