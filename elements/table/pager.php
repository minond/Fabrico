<?php

namespace view\table;

use \view;
use \Fabrico\Core;
use \Fabrico\Element;

// load javascript helper
view\element('action/ui');

class pager extends Element {
	protected static $tag = 'div';
	protected static $getopt = [ 'bindto' ];

	public static function pregen (& $props) {
		$pgr = & Core::$controller->pager;
		$props->content = <<<HTML
<input type="text" class="pagenumber" value="{$pgr->get_page()}" placeholder="Page Number" />
<input type="text" class="pagerpp" value="{$pgr->get_rpp()}" placeholder="Results Per Page" />
HTML;

		view\action\method::open((object) [
			'selector' => "#{$props->id} .pagenumber, #{$props->id} .pagerpp",
			'update' => "$props->bindto, $props->id",
			'action' => 'set_pager_info',
			'on' => 'change'
		]);

		view\param((object) [
			'bindto' => "#{$props->id} .pagenumber"
		]);

		view\param((object) [
			'bindto' => "#{$props->id} .pagerpp"
		]);

		view\action\method::close();
	}
}
