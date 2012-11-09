<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

/**
 * markup parser
 * @uses Lexer
 */
class Parser {
	/**
	 * maximum parser iterations
	 */
	const MAX_ITERATION = 1000;

	/**
	 * parses custom tags and replaces them
	 * with php code
	 * @param Lexer $lexer
	 * @param Closure $cb
	 * @return string
	 */
	public function parse (Lexer & $lexer, \Closure $cb = null) {
		foreach ($lexer->tokens as & $token) {
			$offset = 0;

			for ($i = 0; $i < self::MAX_ITERATION; $i++) {
				preg_match($token::$pattern, $lexer->get_string(), $matches, PREG_OFFSET_CAPTURE, $offset);
				
				if (!count($matches)) {
					break;
				}

				// reset the offset and save in lexer
				$offset = strlen($matches[ 0 ][ 0 ]) + $matches[ 0 ][ 1 ];
				$this->gen_token($lexer, $token, $matches);
			}

			unset($token);
		}

		$orig = $lexer->get_string();
		$html = $this->replace_tokens($lexer);

		if (!is_null($cb) && $cb instanceof \Closure) {
			$cb($orig, $html, $lexer->get_matches());
		}

		return $html;
	}

	/**
	 * @param Lexer $lexer 
	 * @param Token $token 
	 * @param array $matches 
	 * @return void
	 */
	private function gen_token (Lexer & $lexer, Token & $token, array & $matches) {
		$mytoken = clone $token;
		$mytoken->string = $matches[ 0 ][ 0 ];
		$mytoken->parse($matches);
		$lexer->add_match($mytoken);
	}
	
	/**
	 * @param Lexer $lexer 
	 * @return string
	 */
	private function replace_tokens (Lexer & $lexer) {
		$parsedstr = $lexer->get_string();

		foreach ($lexer->get_matches() as $token) {
			$parsedstr = str_replace($token->string, $token->replacement, $parsedstr);
		}

		return $parsedstr;
	}
}
