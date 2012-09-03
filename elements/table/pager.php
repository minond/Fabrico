<?php

namespace view\table;

use \view;
use \Fabrico\html;
use \Fabrico\util;
use \Fabrico\Core;
use \Fabrico\Page;
use \Fabrico\Merge;
use \Fabrico\Element;

// load javascript helper
view\element('action/ui');

class pager extends Element {
	protected static $tag = 'div';
	protected static $getopt = [ 'bindto', 'controls' ];
	protected static $classes = [ 'table_pager' ];

	private static $btn_code = '
$("##{pagerid} .pager_page_move").live("click", function () {
	var $this = $(this);
	Fabrico.controller.method("set_pager_info", [ $this.data("page"), $this.data("rpp") ], [ #{bindto}, "#{pagerid}" ]);
});';

	public static function pregen (& $props) {
		$pgr = & Core::$controller->pager;
		$pages = '';
		$controls = '';

		$previous = html::div([
			'content' => 'prev',
			'data-page' => $pgr->get_previous_page(),
			'data-rpp' => $pgr->get_rpp(),
			'class' => $pgr->has_previous() ? 'pager_link pager_page_move' : 'pager_link disabled'
		]);

		$next = html::div([
			'content' => 'next',
			'data-page' => $pgr->get_next_page(),
			'data-rpp' => $pgr->get_rpp(),
			'class' => $pgr->has_next() ? 'pager_link pager_page_move' : 'pager_link disabled'
		]);

		foreach ($pgr->get_pages() as $page) {
			$pages .= html::div([
				'content' => $page,
				'data-page' => $page,
				'data-rpp' => $pgr->get_rpp(),
				'class' => $page == $pgr->get_page() ? 'pager_selected pager_link' : 'pager_link pager_page_move'
			]);
		}

		// controls
		$rpp = html::input([
			'type' => 'text',
			'class' => 'pagerpp',
			'value' => $pgr->get_rpp()
		]);

		$jumpto = html::input([
			'type' => 'text',
			'class' => 'pagenumber',
			'value' => $pgr->get_page()
		]);

		$rpplabel = html::span([
			'content' => 'results per page: '
		]);

		$jumptolabel = html::span([
			'content' => 'jump to: '
		]);

		$controls = html::div([
			'class' => 'controls',
			'content' => $jumptolabel . $jumpto . $rpplabel . $rpp,
			'style' => [
				'display' => $props->controls ? '' : 'none'
			]
		]);

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

		Page::include_javascript(Merge::parse(self::$btn_code, [
			'pagerid' => $props->id,
			'bindto' => util::csv_string($props->bindto, true)
		]), true, true);

		$props->content = $previous . $pages . $next . $controls;
	}
}
