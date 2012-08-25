<?php

namespace Fabrico;

/**
 * html helpers
 */
class html {
	/**
	 * tag merge fields
	 */
	const CONTENT_KEY = 'content';
	const STYLE_KEY = 'style';
	const OPEN_CLOSE = '<%s %s>%s</%s>';
	const SELF_CLOSE = '<%s %s />';
	const PROP = '%s="%s"';
	const STYLE = '%s: %s;';

	/**
	 * self closing tag elements
	 *
	 * @var array
	 */
	public static $selfclosing = [ 'input', 'img', 'link' ];

	/**
	 * generates a new tag
	 *
	 * @param string tag name
	 * @param string tag properties
	 * @return string tag string
	 */
	public static function generate ($tagname, $props = []) {
		$proplist = [];
		$content = '';

		foreach ($props as $key => $value) {
			if ($key === self::CONTENT_KEY) {
				$content = $value;
			}
			else if ($key === self::STYLE_KEY && is_array($value)) {
				$style = '';

				foreach ($value as $prop => $val) {
					$style .= sprintf(self::STYLE, $prop, $val);
				}

				$proplist[] = sprintf(self::PROP, $key, $style);
			}
			else if (!is_array($value) && !is_object($value)) {
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
			return self::generate($method, count($args) ? $args[ 0 ] : []);
		}
	}
}

/**
 * general helpers
 */
class util {
	/**
	 * matches a string ending
	 *
	 * @param string haystack
	 * @param string needle
	 * @return boolean
	 */
	public static function ends_with ($str, $end) {
		return substr_compare($str, $end, -strlen($end), strlen($end)) === 0;
	}

	/**
	 * returns the last element in an array
	 *
	 * @param array
	 * @return mixed
	 */
	public static function last ($arr) {
		return count($arr) ? $arr[ count($arr) - 1 ] : null;
	}
}
