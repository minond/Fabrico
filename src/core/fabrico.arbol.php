<?php

namespace Fabrico;

class Arbol {
	/**
	 * element id
	 * @var string
	 */
	public $id;

	/**
	 * element tag
	 * @var string
	 */
	public $tag;

	/**
	 * element type
	 * @var string
	 */
	public $type;

	/**
	 * element properties
	 * @var array
	 */
	public $props;

	/**
	 * element child nodes
	 * @var array
	 */
	public $childnodes;

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
	 * @param string element tag
	 * @param string element properties
	 */
	public static function child ($type, $id, $tag, & $props = [], & $children = []) {
		if (count(self::$parents)) {
			self::$parents[ count(self::$parents) - 1 ][] = new self($type, $id, $tag, $props, $children);
		}
		else {
			self::$children[] = new self($type, $id, $tag, $props, $children);
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
	 * @param string element tag
	 * @param string element properties
	 */
	public static function closing ($type, $id, $tag, & $props) {
		$children = array_pop(self::$parents);
		self::child($type, $id, $tag, $props, $children);
	}

	/**
	 * node getter
	 *
	 * @param string element id
	 * @param array of elements to search
	 * @return mixed elemen | array of elements
	 */
	public static function get ($id = '', & $pool = []) {
		if (!$id) {
			return self::$children;
		}
		else {
			if (!count($pool)) {
				$pool = self::$children;
			}

			foreach ($pool as $child) {
				if ($child->id === $id) {
					return $child;
				}
				else if (count($child->childnodes)) {
					$pchild = self::get($id, $child->childnodes);

					if ($pchild !== false) {
						return $pchild;
					}
				}
			}
		}

		return false;
	}

	/**
	 * creates a tree node
	 *
	 * @param string element type
	 * @param string element id
	 * @param string element tag
	 * @param string element properties
	 * @param string element child nodes
	 */
	private function __construct ($type, $id = '', $tag = '', & $props = [], & $children = []) {
		$this->id = $id;
		$this->tag = $tag;
		$this->type = $type;
		$this->props = & $props;
		$this->childnodes = & $children;
	}
}
