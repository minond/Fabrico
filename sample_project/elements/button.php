<?php

class button extends FabricoElement {
	const ACTION = 'fancy_button_action';
	const CREATE = 'fancy_button_create';
	const NORMAL = 'fancy_button_normal';
	const MENU = 'fancy_button_menu';

	protected static $class = array('fancy_button');
	protected static $tag = 'button';

	protected static function pregen ($label, $type = self::NORMAL, $img = false) {
		self::$elem->id = self::gen_id($label);
		self::$elem->class[] = $type;
		self::$elem->content = $label;
	}
}
