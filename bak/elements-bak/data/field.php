<?php

namespace view\data;

use Fabrico\Element;

class input extends Element {
	protected static $tag = 'input';
	protected static $classes = [ 'data_input' ];
}

class text extends input {
	protected static $type = 'text';
	protected static $getopt = [ 'width' ];
	protected static $ignore = [ 'width' ];

	protected static function pregen (& $props) {
		if ($props->width) {
			$props->style = "width: {$props->width}px";
		}
	}
}

class password extends text {
	protected static $type = 'password';
}

class hidden extends input {
	protected static $type = 'hidden';
}

class button extends input {
	protected static $type = 'button';
	protected static $classes = [ 'data_button' ];
}

class submit extends button {
	protected static $type = 'submit';
}

class freset extends button {
	protected static $type = 'reset';
}

class textarea extends Element {
	protected static $tag = 'textarea';
	protected static $classes = [ 'data_textarea' ];
	protected static $getopt = [ 'dim' ];
	protected static $ignore = [ 'dim' ];

	protected static function pregen (& $props) {
		if ($props->dim) {
			list($width, $height) = explode(',', $props->dim);
			$props->style = "height: {$height}px; width: {$width}px";
		}
	}
}
