<?php

class mHTML {
	/**
	 * @name el
	 * @param string element type
	 * @param string element content
	 * @param array element properties
	 * @return string HTML element string
	 */
	public static function el ($type, $content = '', $props = array()) {
		$wrap = '<%s %s>%s</%s>';
		$prop = '%s="%s"';
		$proplist = array();

		foreach ($props as $key => $value) {
			$proplist[] = sprintf($prop, $key, $value);
		}

		$proplist = implode($proplist, ' ');
		return sprintf($wrap, $type, $proplist, $content, $type);
	}
}
