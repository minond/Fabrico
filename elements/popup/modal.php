<?php

namespace view\popup;

use \Fabrico\html;
use \Fabrico\Page;
use \Fabrico\Element;

class modal extends Element {
	protected static $tag = 'div';
	protected static $getopt = [ 'title', 'show' ];
	protected static $ignore = [ 'title', 'show' ];
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

		// background
		$props->content = html::div([
			'class' => 'popup_modal_background'
		]) . $props->content;

		// align
		$props->content = html::center([
			'content' => $props->content
		]);

		Page::include_javascript('Fabrico.ui.popup_resize_center();', true, true);

		if ($props->show) {
			Page::include_javascript("$('#{$props->id}').show();", true, true);
		}

		Page::include_javascript('$(window).trigger("resize");', true, true);
	}
}
