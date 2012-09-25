<?php

namespace view\data;

use Fabrico\Element;

class output extends Element {
	protected static $tag = 'span';
}

class label extends Element {
	protected static $tag = 'label';
}

class block extends Element {
	protected static $tag = 'div';
}

class linkto extends Element {
	protected static $tag = 'a';
}

class form extends Element {
	protected static $tag = 'form';
	protected static $getopt = [ 'class' ];
	protected static $classes = [ 'formbasic' ];
	protected static $styles = [ ['form.css'] ];

	protected static function pregen (& $props) {
		if (in_array('ajax', $props->class)) {
			\Fabrico\Page::include_javascript('Fabrico.ui.no_submit_ajax_form();', true, true);
		}
	}
}
