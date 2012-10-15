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
	 * delete all tokens
	 */
	public function __destruct () {
		unset($this->token);
		unset($this->tokens);
	}

	/**
	 * new raw token setter
	 * @param array $match
	 */
	public function save_raw (array & $match) {
		$token = clone $this->token;
		$token->parse($match);
		$this->tokens[] = $token;
	}
}
