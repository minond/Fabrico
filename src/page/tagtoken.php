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
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		if (count($raw) === self::VALID_MATCH_COUNT) {
			$this->valid = true;
			$this->string = $raw[ 0 ][ 0 ];
			$this->package = $raw[ 1 ][ 0 ];
			$this->namespace = $raw[ 2 ][ 0 ];
			$this->name = $raw[ 3 ][ 0 ];
			$this->properties = $raw[ 4 ][ 0 ];
			$this->type = $this->get_type();
		}
		
		$this->replacement = $this->as_component();
	}

	/**
	 * tag string type checker
	 * @return string
	 */
	private function get_type () {
		if (substr($this->string, 0, 2) === '</') {
			return self::CLOSE;
		}
		else if (substr($this->string, -2, 2) === '/>') {
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
		$properties = '[]';

		return !$this->valid ? '<!-- invalid tag -->' : <<<PHP
<?php
	echo Tag::factory([
		'type' => '{$this->type}',
		'package' => '{$this->package}',
		'namespace' => '{$this->namespace}',
		'name' => '{$this->name}',
		'properties' => (object) {$properties}
	]);
?>
PHP;
	}
}
