<?php

namespace view\lists;

use \Fabrico\html;
use \Fabrico\Merge;
use \Fabrico\Element;

class items extends Element {
	protected static $tag = 'div';
	protected static $classes = [ 'lists_items' ];

	protected static function pregen (& $props) {
		$template = self::param_get('lists_template', $props->param)[ 0 ]->content;

		foreach ($props->data as $data) {
			$props->content .= item::generate([
				'content' => self::merge($template, $data)
			]);
		}
	}
}

class item extends Element {
	protected static $tag = 'div';
	protected static $classes = [ 'lists_item' ];
}

class template extends Element {
	protected static $parameter = true;
}
