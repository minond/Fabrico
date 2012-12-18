<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\util;
use fabrico\core\Mediator;
use fabrico\project\Project;
use fabrico\project\FileFinder;
use fabrico\project\FileLoader;
use fabrico\error\LoggedException;
use fabrico\output\View;
use fabrico\output\Build;
use fabrico\output\Html;
use fabrico\controller\Controller;

/**
 * custom tag generator
 */
class Tag implements FileFinder {
	use Html, Mediator, FileLoader;

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
	private $__children = [];

	/**
	 * tag type (open|single|close)
	 * @see TagToken
	 * @var string
	 */
	private $__type;

	/**
	 * tag's inner content
	 * @var string
	 */
	private $__content = '';

	/**
	 * tag's classes
	 * @var array
	 */
	private $__classes = [];

	/**
	 * argument tags
	 * @var array
	 */
	private $__args = [];

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
	public static function getclass ($package, $namespace = '', $name = '') {
		if ($namespace)
			$namespace = "\\{$namespace}";
		if ($name)
			$name = "\\{$name}";

		return "\\fabrico\\output\\{$package}{$namespace}{$name}";
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
		switch ($this->__type) {
			case TagToken::SINGLE:
			case TagToken::CLOSE:
				$this->initialize();

				if (static::$tag) {
					$props = $this->get_tag_props();

					if (count($this->__classes)) {
						$props['class'] = implode(' ', $this->__classes);
					}

					return self::html(static::$tag, $props, $this->__content);
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
	 * arguments finder
	 * @param string $type
	 * @return Tag[]
	 */
	public function find_args($type) {
		$args = [];
		$class = self::getclass($type);

		foreach ($this->__args as & $arg) {
			if ($arg instanceof $class) {
				$args[] = $arg;
			}

			unset($arg);
		}

		return $args;
	}

	/**
	 * argument finder
	 * @param string $type
	 * @return Tag
	 */
	public function find_arg($type) {
		$class = self::getclass($type);

		foreach ($this->__args as & $arg) {
			if ($arg instanceof $class) {
				return $arg;
			}

			unset($arg);
		}

		return null;
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
	 * class adder
	 * @param string $class
	 */
	public function add_class ($class) {
		$this->__classes[] = $class;
	}

	/**
	 * type getter
	 * @return string
	 */
	public function get_type() {
		return $this->__type;
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
	 * call toString method
	 */
	public function render () {
		(string) $this;
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

	/**
	 * for FileFinder
	 * @return string
	 */
	public static function get_project_file_type() {
		return Project::ELEMENT;
	}

	/**
	 * for FileLoader
	 * @param mixed $identifier
	 * @return string
	 */
	public static function parse_project_file_name($identifier) {
		if (is_string($identifier)) {
			$identifier = explode('/', $identifier);
		}

		list($package, $namespace, $name) = $identifier;
		return implode(DIRECTORY_SEPARATOR, [$package, $namespace, $name]);
	}
}
