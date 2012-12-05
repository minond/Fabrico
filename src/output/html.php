<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * tag generator
 */
trait Html {
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
}
