<?php

class link_to extends FabricoElement {
	protected static $tag = 'a';
	protected static $class = array('fancy_link');

	protected static function pregen ($label, $href = '#') {
		self::$elem->id = self::gen_id($label);
		self::$elem->content = $label;
		self::$elem->href = $href;
	}
}

class link_method extends link_to {
	public static $onready = '$("#%id").click(function () { Fabrico.controller.method_redirect("%method", "%redirect"); });';
	
	protected static function pregen ($label, $method, $redirect) {
		self::$pepper = 'to';
		parent::pregen($label);

		self::$onready_vars = array(
			'id' => self::$elem->id,
			'method' => $method,
			'redirect' => $redirect
		);
	}
}
