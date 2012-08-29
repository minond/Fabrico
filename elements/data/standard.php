<?php

namespace view\data;

class output extends \Fabrico\Element {
	protected static $tag = 'span';
}

class block extends \Fabrico\Element {
	protected static $tag = 'div';
}

class form extends \Fabrico\Element {
	protected static $tag = 'form';
	protected static $getopt = [ 'class' ];

	protected static function pregen (& $props) {
		if (in_array('ajax', $props->class)) {
			\Fabrico\Page::include_javascript('Fabrico.ui.no_submit_ajax_form();', true, true);
		}
	}
}
