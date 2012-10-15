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
	 * default tag package
	 */
	const STD = 'f';

	/**
	 * tag info separator
	 * package:namespace:name
	 */
	const SEP = ':';

	/**
	 * tag pattern
	 * <code>
	 * /
	 *   \<         # tag start
	 *     \/?      # optional closing tag
	 *     (\w?):   # package character
	 *     (\w+?):  # tag namespace
	 *     (\w+)    # tag name
	 *     (.*?)?   # optional tag properties
	 *     \/?      # optional self closing tag
	 *   \>         # tag end
	 * /ms          # multiline, dot all
	 * </code>
	 */
	const TAG = '/\<\/?(\w?):(\w+?):(\w+)(.*?)?\/?\>/ms';

	/**
	 * maximum parser iterations
	 */
	const MAX_ITERATION = 1000;

	/**
	 * @var array
	 */
	private $tags = [];

	/**
	 * custom tag setter
	 * @param string $ns
	 * @param array $tags
	 */
	public function load_tags ($package, $ns, array $tags) {
		if (!isset($this->tags[ $package ])) {
			$this->tags[ $package ] = [];
		}

		$this->tags[ $package ][ $ns ] = $tags;
	}

	/**
	 * parses custom tags and replaces them
	 * with php code
	 * @param string $pattern
	 * @param string $markup
	 * @param Lexer $lexer
	 * @return string
	 */
	public function parse ($pattern, $markup, Lexer $lexer) {
		$offset = 0;

		for ($i = 0; $i < self::MAX_ITERATION; $i++) {
			preg_match($pattern, $markup, $matches, PREG_OFFSET_CAPTURE, $offset);
			
			if (count($matches)) {
				// reset the offset and save in lexer
				$offset = strlen($matches[ 0 ][ 0 ]) + $matches[ 0 ][ 1 ];
				$lexer->save_raw($matches);
			}
			else {
				break;
			}
		}
	}
}

$lexer = new Lexer;
$lexer->token = new TagToken;
$p = new Parser;
$p->load_tags(Parser::STD, 'page', ['def', 'conf']);
$p->load_tags(Parser::STD, 'field', ['text', 'select', 'option', 'checkbox']);

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

$mu2 = $p->parse(Parser::TAG, $mu, $lexer);


\fabrico\core\util::dpr($lexer);

echo $mu . "\n\n";
echo $mu2;



die;