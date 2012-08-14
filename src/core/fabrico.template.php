<?php

namespace Fabrico;

class Template {
	/**
	 * default template
	 *
	 * @var string file path
	 */
	protected static $template;

	/**
	 * minimum variables the template requires and their default values
	 *
	 * @var array
	 */
	protected static $expecting = array();

	/**
	 * loads a template file
	 *
	 * @param array or template variables
	 */
	public static function generate ($_data = array()) {
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

		unset($_var);
		unset($_variable);
		unset($_value);
		unset($_data);

		include template(static::$template);
	}
}
