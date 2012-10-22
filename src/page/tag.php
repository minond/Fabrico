<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

class Tag {
	public static function factory (array $tag) {
		echo "<{$tag['namespace']}:{$tag['name']}>";
	}
}
