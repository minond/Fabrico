<?php

namespace view\resource;

class script extends \Fabrico\Element {
	protected static $tag = 'script';
	protected static $type = 'text/javascript';

	protected static function pregen (& $props) {
		if (isset($props['src'])) {
			\Fabrico\Page::include_javascript(
				\Fabrico\Project::get_javascript_file(
					$props['src'],
					isset($props['core'])
				)
			);
		}
		else {
			\Fabrico\Page::include_javascript(
				$props['content'],
				true, isset($props['onready'])
			);
		}

		return false;
	}
}

class css extends \Fabrico\Element {
	protected static $tag = 'style';
	protected static $type = 'text/css';
	protected static $unique = true;

	protected static function pregen (& $props) {
		if (isset($props['href'])) {
			\Fabrico\Page::include_css(
				\Fabrico\Project::get_css_file(
					$props['href'],
					isset($props['core'])
				)
			);

			return false;
		}
	}
}
