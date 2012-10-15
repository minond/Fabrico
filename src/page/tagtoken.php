<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

/**
 * tag token
 */
class TagToken extends Token {
	/**
	 * tag types
	 * open, close, and self closing tags
	 */
	const OPEN = 'open';
	const CLOSE = 'close';
	const SINGLE = 'single';

	/**
	 * tag package character
	 * @var string
	 */
	private $package;

	/**
	 * tag package namespace
	 * @var string
	 */
	private $namespace;

	/**
	 * tag name
	 * @param string
	 */
	private $name;

	/**
	 * tag type
	 * open, close, self closing
	 * @var string
	 */
	private $type;

	/**
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		$this->string = $raw[ 0 ][ 0 ];
		\fabrico\core\util::dpre($this);
		\fabrico\core\util::dpre($raw);
	}
}
