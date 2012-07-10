<?php

class HTML {
	/**
	 * @name el
	 * @param string element type
	 * @param string element content
	 * @param array element properties
	 * @param boolean no close tag flag
	 * @return string HTML element string
	 */
	public static function el ($type, $props = array(), $noclose = false) {
		$content = '';
		$wrap = '<%s %s>%s</%s>';
		$wrap_open = '<%s %s>%s';
		$prop = '%s="%s"';
		$proplist = array();

		foreach ($props as $key => $value) {
			if ($key === 'content') {
				$content = $value;
			}
			else {
				$proplist[] = sprintf($prop, $key, $value);
			}
		}

		$proplist = implode($proplist, ' ');

		return $noclose ? 
		       sprintf($wrap_open, $type, $proplist, $content) : 
		       sprintf($wrap, $type, $proplist, $content, $type);
	}

	/**
	 * @name style
	 * @param array of style properties
	 * @return string css style string
	 */
	public static function style ($styles) {
		$style = '%s: %s; ';
		$stylelist = array();

		foreach ($styles as $key => $value) {
			$stylelist[] = sprintf($style, $key, $value);
		}

		return implode($stylelist);
	}
}
