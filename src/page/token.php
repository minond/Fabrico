<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

/**
 * basic token
 */
abstract class Token {
	/**
	 * raw matched string
	 * @var string
	 */
	public $string;

	/**
	 * string replacement
	 * @var string
	 */
	public $replacement;

	/**
	 * valid token
	 * token will be able to stringify it self
	 * @var boolean
	 */
	public $valid = false;

	/**
	 * @param array $raw
	 */
	abstract public function parse (array $raw);
}
