<?php

class FabricoTemplateElement {
	protected static $template;
	protected static $templates;
	protected static $expecting = array();

	public static function __callStatic ($template, $data) {
		if (array_key_exists($template, static::$templates)) {
			static::$template = static::$templates[ $template ];
			call_user_func_array(array('static', 'merge'), $data);
		}
		else {
			throw new Exception("invalid template name \"{$template}\"");
		}
	}

	public static function merge ($_data = array()) {
		if (is_array($_data)) {
			foreach ($_data as $_var => $_value) {
				$$_var = $_value;
			}
		}

		if (count(static::$expecting)) {
			foreach (static::$expecting as $_var) {
				if (!array_key_exists(is_array($_var) ? $_var[ 0 ] : $_var, $_data)) {
					if (is_array($_var)) {
						list($_variable, $_value) = $_var;
						$$_variable = $_value;
					}
					else {
						$$_var = '';
					}
				}
			}
		}

		include template(static::$template);
	}
}
