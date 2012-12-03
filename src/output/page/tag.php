<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\util;
use fabrico\core\Mediator;
use fabrico\core\Project;
use fabrico\error\LoggedException;
use fabrico\output\View;
use fabrico\output\Build;
use fabrico\controller\Controller;

/**
 * custom tag generator
 */
class Tag {
	/**
	 * used to pass properties from an open
	 * tag to its closing tag when generating
	 * its content and html
	 * @var array
	 */
	private static $propstack = [];

	/**
	 * argument tags stack
	 * @var array
	 */
	private static $argstack = [[]];

	/**
	 * tag name
	 * set if this element should be rendered as html
	 * @var string
	 */
	protected static $tag = false;

	/**
	 * arg flag
	 * set if this element should saved as an argument
	 * @var boolean
	 */
	protected static $arg = false;

	/**
	 * properties to be added to the tag html
	 * custom property name can be set as the
	 * key's value
	 * @var array
	 */
	protected static $tagopt = [];

	/**
	 * child tags
	 * @var Tag[]
	 */
	protected $__children = [];

	/**
	 * tag type (open|single|close)
	 * @see TagToken
	 * @var string
	 */
	protected $__type;

	/**
	 * tag's inner content
	 * @var string
	 */
	protected $__content = '';

	/**
	 * argument tags
	 * @var array
	 */
	protected $__args = [];

	/**
	 * standard properties - id
	 * @var string
	 */
	protected $id;

	/**
	 * standard properties - name
	 * @var string
	 */
	protected $name;

	/**
	 * @param mixed $props
	 * @param string $type
	 */
	public function __construct ($props = null, $type = TagToken::SINGLE) {
		$this->__type = $type;

		if (is_null($props)) {
			$props = [];
		}

		self::prepare($this, $props);
	}

	/**
	 * tag format: <package:namespace:name />
	 * namespace format: fabrico\output\package\namespace
	 * class format: name
	 * @param string $package
	 * @param string $ns
	 * @param string $name
	 * @return string
	 */
	public static function getclass ($package, $namespace, $name) {
		return "\\fabrico\\output\\{$package}\\{$namespace}\\{$name}";
	}

	/**
	 * standard tag factory
	 * @param array $tag
	 * @return Tag
	 */
	public static function factory (array $tag) {
		$el = self::getclass($tag['package'], $tag['namespace'], $tag['name']);

		if (!class_exists($el)) {
			throw new LoggedException("Invalid tag: {$el}");
		}

		return new $el($tag['properties'], $tag['type']);
	}

	/**
	 * manages the property stack and sets it's content
	 * @param Tag $tag
	 * @param array $props
	 */
	private static function prepare (Tag & $tag, array & $props) {
		switch ($tag->__type) {
			case TagToken::OPEN:
				// start gathering this tag's conten,
				ob_start();

				// stack it's properties and create an
				// argument stack for it
				self::$propstack[] = & $props;
				self::$argstack[ count(self::$argstack) ] = [];
				break;

			case TagToken::CLOSE:
				// get it's open tag's properties, arguments,
				// and content
				$props = array_pop(self::$propstack);
				$tag->__args = array_pop(self::$argstack);
				$tag->__content = trim(ob_get_clean());
				$tag->sets($props);
				break;

			case TagToken::SINGLE:
				// set the properties
				$tag->sets($props);
				break;

			default:
				throw new LoggedException("Invalid tag type: {$tag->__type}");
				break;
		}

		// add self to argument stack if needed
		if ($tag::$arg === true) {
			self::$argstack[ count(self::$argstack) - 1 ][] = & $tag;
		}
	}

	/**
	 * generates html. creates a tag, properties, content
	 * @param string $tag
	 * @param mixed $props
	 * @param string $content
	 * @return string
	 */
	public static function html ($tag, $props = [], $content = '') {
		$propstr = '';

		foreach ($props as $prop => $val) {
			$propstr .= " {$prop}=\"{$val}\"";
		}

		return "<{$tag}{$propstr}" .
		       (in_array($tag, ['input', 'img']) ?
		       " />" : ">{$content}</{$tag}>");
	}

	/**
	 * generates a tag property array that will
	 * be used when generating the html
	 * @return array
	 */
	private function get_tag_props () {
		$props = [];

		foreach (static::$tagopt as $prop => $name) {
			$val = $this->{ is_numeric($prop) ? $name : $prop };

			if (strlen($val)) {
				$props[ $name ] = $val;
			}
		}

		return $props;
	}

	/**
	 * if the tag it either a single or closing tag
	 * it will generate it's html and return it
	 * @return string
	 */
	final public function __toString () {
		/*
		$this->initialize();

		foreach ($this->__children as $child) {
			$this->__content .= (string) $child;
		}
		*/

		switch ($this->__type) {
			case TagToken::SINGLE:
			case TagToken::CLOSE:
				$this->initialize();

				if (static::$tag) {
					return self::html(static::$tag, $this->get_tag_props(), $this->__content);
				}

				break;
			}

		return '';
	}

	/**
	 * sets  the tag's properties
	 * @param array $props
	 */
	public function sets (array & $props = null) {
		if (is_array($props)) {
			foreach ($props as $prop => $value) {
				$this->set($prop, $value);
			}
		}
	}

	/**
	 * sets a tag property
	 * @param string $prop
	 * @param string $value
	 */
	public function set ($prop, $value) {
		if (property_exists($this, $prop)) {
			$this->{ $prop } = $value;
		}
		else {
			$me = get_class($this);
			throw new LoggedException("Invalid property \"{$prop}\" for \"{$me}\"");
		}
	}

	/**
	 * content getter
	 * @return string
	 */
	public function get_content () {
		return $this->__content;
	}

	/**
	 * content setter
	 * @param string $content
	 * @return string
	 */
	public function set_content ($content) {
		$this->__content = $content;
	}

	/**
	 * add a child tag
	 * @param Tag $tag
	 */
	public function add_child (Tag & $tag) {
		if ($tag === $this) {
			throw new LoggedException('Cannot add tag as child of itself');
		}

		$this->__children[] = & $tag;
	}

	/**
	 * virtual
	 * if declared by tag, function is called on when the page
	 * is compiled and used as the tag's replacement.
	 * @see Page::prepare
	 * @return mixed
	 */
	public function assemble () {
		return false;
	}

	/**
	 * virtual
	 * called right before the element's
	 * html it generated for output
	 * @return void
	 */
	protected function initialize () {
		return;
	}
}
