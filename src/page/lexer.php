<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

/**
 * custom tag tokenizer
 * @uses TagToken
 */
class Lexer {
	/**
	 * @var Token
	 */
	public $token;

	/**
	 * match tokens
	 * @var Tokens[]
	 */
	private $tokens = [];

	/**
	 * new raw token setter
	 * @param array $match
	 */
	public function save_raw (array & $match) {
		$token = clone $this->token;
		$this->tokens[] = $token->parse($match);
	}
}
