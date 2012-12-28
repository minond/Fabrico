<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * json output manager
 */
class Json extends OutputContent implements \JsonSerializable {
	/**
	 * internal storage
	 * @var stdClass
	 */
	private $vars;

	/**
	 * generates new std class
	 * @param array $varls
	 */
	public function __construct (array $vals = null) {
		$this->vars = new \stdClass;
		$this->load($vals);
	}

	/**
	 * @param array $vals
	 */
	public function load(array $vals = null) {
		if (is_array($vals)) {
			foreach ($vals as $var => $val) {
				$this->__set($var, $val);
			}
		}
	}

	/**
	 * @param string $var
	 * @return mixed
	 */
	public function __get ($var) {
		return isset($this->vars->{ $var }) ? $this->vars->{ $var } : null;
	}

	/**
	 * @param string $var
	 * @param mixed $var
	 * @return mixed
	 */
	public function __set ($var, $val) {
		return $this->vars->{ $var } = $val;
	}

	/**
	 * so it checks self::$vars
	 * @param string $var
	 * @return boolean
	 */
	public function __isset ($var) {
		return isset($this->vars->{ $var });
	}

	/**
	 * so it checks self::$vars
	 * @param string $var
	 */
	public function __unset ($var) {
		unset($this->vars->{ $var });
	}

	/**
	 * json_encode shortcut
	 * @return string
	 */
	public function __toString() {
		return json_encode($this);
	}

	/**
	 * merges the template with user data
	 * @return string
	 */
	public function render ($type) {
		return json_encode($this->vars, JSON_PRETTY_PRINT);
	}

	/**
	 * for json_encode
	 * @return stdClass
	 */
	public function jsonSerialize () {
		return $this->vars;
	}
}
