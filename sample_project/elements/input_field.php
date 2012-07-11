<?php

class input_field extends FabricoElement {
	protected static $class = array('input_field');
	protected static $tag = 'input';

	protected static function pregen ($name) {
		$invalid = Fabrico::is_invalid($name);
		self::is_invalid($invalid);

		if ($invalid) {
			self::$elem->autofocus = 'autofocus';
		}

		self::$elem->autocomplete = 'off';
		self::$elem->spellcheck = 'false';
		self::$elem->name = $name;
		self::$elem->value = Fabrico::req($name);
	}
}

class password_field extends input_field {
	protected static $type = 'password';
}

class testing extends FabricoElement {
	protected static function pregen ($b, $a, $c) {
		self::$elem->content = "$b - $a (>',')> $c";
	}
}
