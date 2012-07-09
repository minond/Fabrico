<?php

class toolbar_item extends FabricoTemplate {
	protected static $class = array('toolbar_item');

	protected static function pregen ($title) {
		self::$elem->id = self::gen_id($title);
		self::$elem->content = $title;
		self::is_active(self::gen_name($title) === Fabrico::$file);
	}
}
