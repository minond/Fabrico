<?php

namespace Fabrico;

class html {
	/**
	 * tag merge fields
	 */
	const CONTENT_KEY = 'content';
	const OPEN_CLOSE = '<%s %s>%s</%s>';
	const SELF_CLOSE = '<%s %s />';
	const PROP = '%s="%s"';

	/**
	 * self closing tag elements
	 *
	 * @var array
	 */
	public static $selfclosing = array('input', 'img', 'link');

	/**
	 * generates a new tag
	 *
	 * @param string tag name
	 * @param string tag properties
	 * @return string tag string
	 */
	public static function generate ($tagname, $props = array()) {
		$proplist = array();
		$content = '';

		foreach ($props as $key => $value) {
			if ($key === self::CONTENT_KEY) {
				$content = $value;
			}
			else {
				$proplist[] = sprintf(self::PROP, $key, $value);
			}
		}


		return in_array($tagname, self::$selfclosing) ?
		       sprintf(self::SELF_CLOSE, $tagname, implode(' ', $proplist)) :
		       sprintf(self::OPEN_CLOSE, $tagname, implode(' ', $proplist), $content, $tagname);
	}

	/**
	 * overwirte static calls
	 */
	public static function __callStatic ($method, $args) {
		if (!method_exists('self', $method)) {
			return self::generate($method, count($args) ? $args[ 0 ] : array());
		}
	}
}
