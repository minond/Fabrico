<?php

class form_action extends FabricoElement {
	protected static $tag = 'form';

	protected static function pregen ($action, $success, $fail, $content = false) {
		$data_fields = '';
		self::$elem->method = 'post';

		// method
		$data_fields .= html::el('input', array(
			'type' => 'hidden',
			'name' => Fabrico::$uri_query_method,
			'value' => $action
		));

		// success url
		$data_fields .= html::el('input', array(
			'type' => 'hidden',
			'name' => Fabrico::$uri_query_success,
			'value' => $success
		));

		// failure url
		$data_fields .= html::el('input', array(
			'type' => 'hidden',
			'name' => Fabrico::$uri_query_fail,
			'value' => $fail
		));

		if ($content === false) {
			self::$elem->noclose = true;
			self::$posthtml = $data_fields;
		}
		else {
			self::$elem->content = $data_fields . $content;
		}
	}
}
