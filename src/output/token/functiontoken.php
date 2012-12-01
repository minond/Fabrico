<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * merge field token
 */
class FunctionToken extends Token {
	/**
	 * @var string
	 */
	public static $pattern = '/!{(.+?)}/';

	/**
	 * holder name
	 * @var string
	 */
	public static $holder = MergeToken::IN_PHP;

	/**
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		list($find, $replace) = MergeToken::getspecial();
		$part = explode(' ', $raw[1][0]);
		$func = array_shift($part);
		$func = str_replace($find, $replace, $func);
		$func = MergeToken::clean_var($func);
		$vars = [];

		foreach ($part as $var) {
			$var = str_replace($find, $replace, $var);
			$vars[] = MergeToken::clean_var($var);
		}

		$vars = implode(', ', $vars);
		$this->valid = true;
		$this->replacement = sprintf('%s(%s)', $func, $vars);
		$this->replacement = MergeToken::mergeholder($this->replacement, self::$holder);
	}
}


function out ($one, $two) {
	echo $one;
}
