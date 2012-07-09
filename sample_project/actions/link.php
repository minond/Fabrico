<?php

class link_to extends FabricoTemplate {
	protected static $tag = 'a';
	protected static $class = array('fancy_link');

	protected static function pregen ($label, $href = '#') {
		self::$elem->content = $label;
		self::$elem->href = $href;
	}
}
