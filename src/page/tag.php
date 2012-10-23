<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\util;

/**
 * custom tag generator
 */
class Tag {
	/**
	 * base namespace for all tags
	 */
	const BASE_NS = '\\fabrico\\page\\';

	/**
	 * tag name
	 * @var string
	 */
	protected $tag;

	/**
	 * tag properties
	 * @var stdClass
	 */
	protected $props;

	/**
	 * @param string $package
	 * @param string $ns
	 * @param string $name
	 * @return string
	 */
	private static function getclass ($package, $namespace, $name) {
		return self::BASE_NS . "{$name}";
		return self::BASE_NS . "{$package}\\{$namespace}\\{$name}";
	}

	/**
	 * standard tag factory
	 * @param array $tag
	 * @return Tag
	 */
	public static function factory (array $tag) {
		$el = self::getclass($tag['package'], $tag['namespace'], $tag['name']);

		if (class_exists($el)) {
			$el = new $el;
		}
		else {
			util::dpre("Invalid tag: $el");
		}

		echo $el;
		util::dpre($tag);
	}

	/**
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
	 * @return string
	 */
	final public function __toString () {
		return "";
	}

	/**
	 * @param stdClass $props
	 * @return void
	 */
	protected function prepare (\stdClass & $props) {
		
	}
}

class Text extends Tag {
	protected $tag = 'input';
}
