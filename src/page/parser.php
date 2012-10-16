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
	 * @return string
	 */
	public function parse (Lexer $lexer) {
		foreach ($lexer->tokens as & $token) {
			$offset = 0;

			for ($i = 0; $i < self::MAX_ITERATION; $i++) {
				preg_match($token::$pattern, $lexer->get_string(), $matches, PREG_OFFSET_CAPTURE, $offset);
				
				if (count($matches)) {
					// reset the offset and save in lexer
					$offset = strlen($matches[ 0 ][ 0 ]) + $matches[ 0 ][ 1 ];
					$mytoken = clone $token;
					$mytoken->parse($matches);
					$lexer->add_match($mytoken);
				}
				else {
					break;
				}
			}

			unset($token);
		}
	}
}

$mu = <<<MU
</f:page:conf title="Add New User"                 />
<f:page:def controller="UserManager" format="html, mobile, pdf" />
<f:page:def>
<f:page:def
controller="UserManager" 
format="html, mobile, pdf"
/>
	<f:page:conf title="Add New User" />

	<f:field:text name="first_name" id="first_name_field" />
	<f:field:select name="sel" id="sel_field">
		<f:field:option value="one" label="1" />
		<f:field:option value="two">2</f:field:option>
	</f:field:select>
</f:page:def>
MU;

$lexer = new Lexer;
$lexer->add_token(new TagToken);
$lexer->set_string($mu);

$parser = new Parser;
$parser->parse($lexer);

\fabrico\core\util::dpr($lexer);


die;
