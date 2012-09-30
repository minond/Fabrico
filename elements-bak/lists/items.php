<?php

namespace view\lists;

use \Fabrico\html;
use \Fabrico\Merge;
use \Fabrico\Element;

/**
 * items holder
 */
class items extends Element {
	protected static $tag = 'div';
	protected static $classes = [ 'lists_items' ];

	protected static function pregen (& $props) {
		$template = template::first($props)->content;

		foreach ($props->data as $index => $data) {
			$itemtemplate = preg_replace('/{#iteration}/', $index, $template);

			if ($index) {
				$props->content .= html::div([
					'class' => 'lists_item_separator'
				]);
			}

			$props->content .= item::generate([
				'content' => self::merge($itemtemplate, $data)
			]);
		}
	}
}

/**
 * item element
 */
class item extends Element {
	protected static $tag = 'div';
	protected static $classes = [ 'lists_item' ];
}

/**
 * item content template
 */
class template extends Element {
	protected static $parameter = true;
}
