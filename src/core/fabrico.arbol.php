<?php

namespace Fabrico;

class Arbol {
	/**
	 * element id
	 * @var string
	 */
	public $id;

	/**
	 * element type
	 * @var string
	 */
	public $type;

	/**
	 * element properties
	 * @var array
	 */
	private $props;

	/**
	 * element child nodes
	 * @var array
	 */
	private $childnodes;

	/**
	 * child stack
	 * @var array
	 */
	private static $children = [];
	
	/**
	 * child stack
	 * @var array
	 */
	private static $parents = [];

	/**
	 * adds a new element to the node/child stack
	 *
	 * @param string element type
	 * @param string element id
	 * @param string element properties
	 */
	public static function child ($type, $id, $props) {
		if (count(self::$parents)) {
			self::$parents[ count(self::$parents) - 1 ][] = new self($type, $id/*, $props*/);
		}
		else {
			self::$children[] = new self($type, $id, $props);
		}
	}

	/**
	 * adds a new element to the parent stack
	 *
	 * @param string element type
	 */
	public static function opening ($type) {
		self::$parents[] = [];
	}

	/**
	 * moves an element from the parent stack onto the child stack
	 *
	 * @param string element type
	 * @param string element id
	 * @param string element properties
	 */
	public static function closing ($type, $id, $props) {
		self::$children[] = new self($type, $id, $props, array_pop(self::$parents));
	}

	/**
	 * creates a tree node
	 *
	 * @param string element type
	 * @param string element id
	 * @param string element properties
	 * @param string element child nodes
	 */
	private function __construct ($type, $id = '', $props = [], $children = []) {
		$this->type = $type;
		$this->props = $props;
		$this->id = $id;
		$this->childnodes = $children;
	}
}
