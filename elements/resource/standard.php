<?php

namespace resource;

class script extends \Fabrico\Element {
	protected static $tag = 'script';
	protected static $type = 'text/javascript';

	protected static function pregen (& $props) {
		if (isset($props['src'])) {
			if (isset($props['core'])) {
				$src = '';
			}
			else {
				$src = \Fabrico\Project::get_javascript_file($props['src']);
			}

			\Fabrico\Page::include_javascript($src);
			return false;
		}
		else {

		}
	}
}

class css extends \Fabrico\Element {
	protected static $tag = 'style';
	protected static $type = 'text/css';

	protected static function pregen (& $props) {
		if (isset($props['href'])) {
			\Fabrico\Page::include_css($props['href']);
			return false;
		}
	}
}
