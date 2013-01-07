<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output\modal;

/**
 * popup header
 */
class Header extends \fabrico\output\Tag {
	protected static $arg = true;

	protected function initialize() {
		$this->set_content($this->get_content() . ' - from v1');
	}
}
