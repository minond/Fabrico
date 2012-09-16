<?php

namespace view\popup;

use \Fabrico\html;
use \Fabrico\Element;

class modal extends Element {
	protected static $tag = 'div';
	protected static $getopt = [ 'title' ];
	protected static $ignore = [ 'title' ];
	protected static $classes = [ 'popup_modal' ];

	protected static function pregen (& $props) {
		$title = '';

		// title
		if ($props->title) {
			$close = \view\resource\img::generate((object) [
				'core' => true,
				'src' => 'close.gif',
				'onclick' => '$(this.parentNode.parentNode.parentNode.parentNode).hide();'
			]);

			$title = html::div([
				'class' => 'popup_modal_content_title',
				'content' => $props->title . $close
			]);
		}

		// body
		$props->content = $title . html::div([
			'class' => 'popup_modal_content_body',
			'content' => $props->content
		]);

		// content
		$props->content = html::div([
			'class' => 'popup_modal_content',
			'content' => $props->content
		]);

		// border
		$props->content = html::div([
			'class' => 'popup_modal_border',
			'content' => $props->content
		]);

		$props->content = html::div([
			'class' => 'popup_modal_background'
		]) . $props->content;
	}
}
