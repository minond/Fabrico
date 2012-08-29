<?php

namespace view\resource;

/**
 * javascript
 */
class script extends \Fabrico\Element {
	protected static $tag = 'script';
	protected static $type = 'text/javascript';
	protected static $getopt = [ 'src', 'core', 'content', 'onready' ];

	protected static function pregen (& $props) {
		if ($props->src) {
			\Fabrico\Page::include_javascript(
				\Fabrico\Project::get_javascript_file($props->src, (bool) $props->core)
			);
		}
		else {
			\Fabrico\Page::include_javascript($props->content, true, (bool) $props->onready);
		}

		return false;
	}
}

/**
 * css
 */
class style extends \Fabrico\Element {
	protected static $tag = 'style';
	protected static $type = 'text/css';
	protected static $getopt = [ 'href', 'core' ];

	protected static function pregen (& $props) {
		if ($props->href) {
			\Fabrico\Page::include_css(
				\Fabrico\Project::get_css_file($props->href, (bool) $props->core)
			);

			return false;
		}
	}
}

/**
 * image
 */
class img extends \Fabrico\Element {
	protected static $tag = 'img';
	protected static $getopt = [ 'src' ];

	protected static function pregen (& $props) {
		$props->src = \Fabrico\Project::get_image_file($props->src);
	}
}

/**
 * resource file content
 */
class raw extends \Fabrico\Element {
	protected static $tag = 'pre';
	protected static $getopt = [ 'file' ];

	protected static function pregen (& $props) {
		$props->content = file_get_contents(
			\Fabrico\Project::get_resource_file($props->file)
		);
	}
}
