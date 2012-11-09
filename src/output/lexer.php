<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * custom tag tokenizer
 * @uses TagToken
 */
class Lexer {
	/**
	 * tokens used by this lexer
	 * @var Token[]
	 */
	public $tokens = [];

	/**
	 * match tokens
	 * @var Tokens[]
	 */
	private $matches = [];

	/**
	 * raw string
	 * @var string
	 */
	private $str = '';

	/**
	 * delete all tokens
	 */
	public function __destruct () {
		unset($this->matches);
		unset($this->tokens);
	}

	/**
	 * @return Token[]
	 */
	public function get_matches () {
		return $this->matches;
	}

	/**
	 * @param Token $token
	 */
	public function add_match (Token & $token) {
		$this->matches[] = $token;
	}

	/**
	 * @param Token $token
	 */
	public function add_token (Token & $token) {
		$this->tokens[] = $token;
	}

	/**
	 * @param string $str
	 */
	public function set_string ($str) {
		$this->str = $str;
	}

	/**
	 * @return string
	 */
	public function get_string () {
		return $this->str;
	}
}
