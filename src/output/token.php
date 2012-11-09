<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * basic token
 */
abstract class Token {
	/**
	 * token's regular expression
	 * @var string
	 */
	public static $pattern = '/.+/';

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
