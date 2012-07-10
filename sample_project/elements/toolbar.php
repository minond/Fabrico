<?php

class toolbar_item extends FabricoTemplate {
	protected static $class = array('toolbar_item');
	public static $onready = '$("#%id").click(function () { location.href = "/%url"; });';

	protected static function pregen ($title) {
		$file = self::gen_name($title);
		self::$elem->id = $id = self::gen_id($title);
		self::$elem->content = $title;
		self::is_active($file === Fabrico::$file);

		self::$onready_vars = array(
			'id' => $id,
			'url' => $file
		);
	}
}
