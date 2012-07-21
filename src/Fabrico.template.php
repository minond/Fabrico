<?php

class FabricoTemplateElement {
	protected static $template;
	protected static $templates;

	public static function __callStatic ($template, $data) {
		if (array_key_exists($template, static::$templates)) {
			static::$template = static::$templates[ $template ];
			call_user_func_array(array('static', 'merge'), $data);
		}
		else {
			throw new Exception("invalid template name");
		}
	}

	public static function merge ($data) {
		if (is_array($data)) {
			foreach ($data as $var => $value) {
				$$var = $value;
			}
		}

		include template(static::$template);
	}
}
