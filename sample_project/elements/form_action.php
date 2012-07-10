<?php

class form_action extends FabricoTemplate {
	protected static $tag = 'form';
	protected static $noclose = true;

	protected static function pregen ($action, $success, $fail) {
		self::$elem->method = 'post';

		// method
		self::$posthtml .= HTML::el('input', array(
			'type' => 'hidden',
			'name' => Fabrico::$uri_query_method,
			'value' => $action
		));

		// success url
		self::$posthtml .= HTML::el('input', array(
			'type' => 'hidden',
			'name' => Fabrico::$uri_query_success,
			'value' => $success
		));

		// failure url
		self::$posthtml .= HTML::el('input', array(
			'type' => 'hidden',
			'name' => Fabrico::$uri_query_fail,
			'value' => $fail
		));
	}
}
