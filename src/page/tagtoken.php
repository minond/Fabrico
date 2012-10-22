<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

/**
 * tag token
 * @uses PropertyToken
 */
class TagToken extends Token {
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
	 * @var string
	 */
	public static $pattern = '/\<\/?(\w+?):(\w+?):(\w+)(.*?)?\/?\>/ms';

	/**
	 * identifier string matching self closing tags
	 * @var string
	 */
	public static $single_identifier = '/>';

	/**
	 * identifier string matching closing tags
	 * @var string
	 */
	public static $close_identifier = '</';

	/**
	 * number of matches a valid raw token should have
	 */
	const VALID_MATCH_COUNT = 5;

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
	 * raw properties string
	 * @var sting
	 */
	private $properties;

	/**
	 * @var PropertyToken
	 */
	private $property_token;

	/**
	 * @var string
	 */
	public static $valid_tag = <<<PHP
<?php echo \\fabrico\\page\\Tag::factory([
 'type' => '%type',
 'package' => '%package',
 'namespace' => '%namespace',
 'name' => '%name',
 'properties' => (object) [ %properties ]
]); ?>
PHP;

	/**
	 * @var string
	 */
	public static $invalid_tag = '<!-- invalid tag -->';

	/**
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		if (count($raw) === self::VALID_MATCH_COUNT) {
			$this->valid = true;
			$this->package = $raw[ 1 ][ 0 ];
			$this->namespace = $raw[ 2 ][ 0 ];
			$this->name = $raw[ 3 ][ 0 ];
			$this->type = $this->get_type();

			// prop tokenizer
			$this->properties = $raw[ 4 ][ 0 ];
			$this->property_token = new PropertyToken;
			$this->property_token->parse(array($this->properties));
		}
		
		$this->replacement = $this->as_component();
	}

	/**
	 * tag string type checker
	 * @return string
	 */
	private function get_type () {
		if (substr($this->string, 0, 2) === self::$close_identifier) {
			return self::CLOSE;
		}
		else if (substr($this->string, -2, 2) === self::$single_identifier) {
			return self::SINGLE;
		}
		else {
			return self::OPEN;
		}
	}

	/**
	 * returns the php code used to create this element
	 * @return string
	 */
	private function as_component () {
		return !$this->valid ? self::$invalid_tag :
			str_replace(
				['%type', '%package', '%namespace', '%name', '%properties'],
				[$this->type, $this->package, $this->namespace, $this->name, $this->property_token->replacement],
				self::$valid_tag
			);
	}
}
