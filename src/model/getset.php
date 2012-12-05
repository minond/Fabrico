<?php

/**
 * @package fabrico\model
 */
namespace fabrico\model;

/**
 * property getter and setter helper
 */
trait GetSet {
	/**
	 * property getter, calls property getter
	 * @param strint $name
	 * @return mixed
	 */
	public function __get($name) {
		if (method_exists($this, "get_{$name}")) {
			return $this->{ "get_{$name}" }();
		}
		else if (property_exists($this, $name)) {
			return $this->{ $name };
		}
		else {
			throw new \Exception(
				sprintf('Getting invalid property "%s" of "%s"', $name, get_class($this))
			);
		}
	}

	/**
	 * property setter, calls property setter
	 * @param string $name
	 * @param mixed $val
	 */
	public function __set($name, $val) {
		if (method_exists($this, "set_{$name}")) {
			$this->{ "set_{$name}" }($val);
		}
		else if (property_exists($this, $name)) {
			$this->{ $name } = $val;
		}
		else {
			throw new \Exception(
				sprintf('Setting invalid property "%s" of "%s"', $name, get_class($this))
			);
		}
	}

	/**
	 * property check
	 * @param string $var
	 * @return boolean
	 */
	public function __isset ($var) {
		return property_exists($this, $var) &&
			$this->{ $var } !== null;
	}

	/**
	 * property unsetter
	 * @var string $var
	 */
	public function __unset ($var) {
		if (property_exists($this, $var)) {
			$this->{ $var } = null;
		}
	}
}
