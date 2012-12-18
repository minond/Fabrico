<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\modal;

/**
 * popup content
 */
class Body extends \fabrico\output\Tag {
	protected static $arg = true;

	/**
	 * popup body height
	 * @var string
	 */
	public $height = 'auto';

	/**
	 * popup body width
	 * @var string
	 */
	public $width = 'auto';
}
