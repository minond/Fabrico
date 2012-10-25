<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\util;
use fabrico\error\LoggedException;

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
	 * tag name
	 * @var string
	 */
	protected static $tag;

	/**
	 * properties to be added to the tag html
	 * custom property name can be set as the
	 * key's value
	 * @var array
	 */
	protected static $tagopt = [];

	/**
	 * tag type (open|single|close)
	 * @see TagToken
	 * @var string
	 */
	public $__type;

	/**
	 * tag's inner content
	 * @var string
	 */
	public $__content = '';

	/**
	 * tag format: <package:namespace:name />
	 * namespace format: fabrico\page\package\namespace
	 * class format: name
	 * @param string $package
	 * @param string $ns
	 * @param string $name
	 * @return string
	 */
	private static function getclass ($package, $namespace, $name) {
		return "\\fabrico\\page\\{$name}";
		return "\\fabrico\\page\\{$package}\\{$namespace}\\{$name}";
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

		$el = new $el;
		$el->__type = $tag['type'];
		self::prepare($el, $tag['properties']);
		return $el;
	}

	/**
	 * manages the property stack and sets it's content
	 * @param Tag $tag
	 * @param stdClass $props
	 */
	private static function prepare (Tag & $tag, \stdClass & $props) {
		switch ($tag->__type) {
			case TagToken::OPEN:
				ob_start();
				self::$propstack[] = & $props;
				break;

			case TagToken::CLOSE:
				$props = array_pop(self::$propstack);
				$tag->__content = trim(ob_get_clean());

			case TagToken::SINGLE:
				$tag->sets($props);
				$tag->initialize();
				break;

			default:
				throw new LoggedException("Invalid tag type: {$tag->__type}");
				break;
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
		       (in_array($tag, ['input']) ?
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
			$props[ $name ] = $this->{ is_numeric($prop) ? $name : $prop };
		}
		
		return $props;
	}

	/**
	 * if the tag it either a single or closing tag
	 * it will generate it's html and return it
	 * @return string
	 */
	final public function __toString () {
		switch ($this->__type) {
			case TagToken::SINGLE:
			case TagToken::CLOSE:
				return self::html(static::$tag, $this->get_tag_props(), $this->__content);
		}

		return '';
	}

	/**
	 * sets  the tag's properties
	 * @param stdClass $props 
	 */
	public function sets (\stdClass & $props) {
		foreach ($props as $prop => $value) {
			$this->set($prop, $value);
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
	 * virtual
	 * called right before the element's
	 * html it generated for output
	 * @return void
	 */
	protected function initialize () {}
}

class Text extends Tag {
	protected static $tag = 'input';
	protected static $tagopt = [ 'name', 'user' => 'data-us' ];
	public $name;
	public $user;
	protected function initialize () {
		$this->name .= "_input_field";
	}
}

class Div extends Tag {
	protected static $tag = 'div';
}
