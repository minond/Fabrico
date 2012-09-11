<?php

namespace view\output;

use \Fabrico\Element;
use \Fabrico\Project;

class wiki extends Element {
	protected static $tag = 'span';

	protected static function pregen (& $props) {
		require_once Project::get_dependency_file('WikiParser/WikiParser.php');

		$wikiparser = new \WikiParser();
		$props->content = $wikiparser->parse(preg_replace("/^(.*)\n/", "\\1", $props->content));
	}
}
