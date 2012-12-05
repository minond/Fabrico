<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * tag property token
 * @uses MergeToken
 */
class PropertyToken extends Token {
	/**
	 * @var array
	 */
	public $properties;

	/**
	 * @see Token::parse
	 */
	public function parse (array $match) {
		list($props, $vals) = $this->get_prop_val($match[ 0 ]);
		$this->replacement = $this->get_props_str($props, $vals);
		$this->valid = true;
	}

	/**
	 * @param array $props
	 * @param array $vals
	 * @return string
	 */
	private function get_props_str (array $props, array $vals) {
		$propstrs = [];
		$propmap = [];

		foreach ($props as $index => $prop) {
			if (isset($vals[ $index ])) {
				$prop = trim($prop);
				$propmap[ $prop ] = $this->parse_value($vals[ $index ]);
				// $this->properties[ $prop ] = $this->parse_value($vals[ $index ]);
				// $this->properties[ $prop ] = $this->real_nice_value($this->parse_value($vals[ $index ], MergeToken::IN_PHP));
				$this->properties[ $prop ] = $this->real_nice_value($vals[ $index ]);
			}
		}


		foreach ($propmap as $property => $value) {
			$propstrs[] = $this->array_prop($property, $value);
		}

		return implode(', ', $propstrs);
	}

	/**
	 * @param string $propstr
	 * @return array[array]
	 */
	private function get_prop_val ($propstr) {
		$parts = explode('=', trim($propstr));
		$lastprop = count($parts) - 1;
		$props = [];
		$vals = [];

		foreach ($parts as $index => $part) {
			if (!$index) {
				$props[] = $part;
			}
			else if ($index === $lastprop) {
				$vals[] = $part;
			}
			else {
				$strparts = explode(' ', $part);
				$strparts = preg_split('/\s+(?=\S*+$)/', rtrim($part));
				$props[] = $strparts[ 1 ];
				$vals[] = $strparts[ 0 ];
			}
		}

		return [ $props, $vals ];
	}

	/**
	 * @param string $property
	 * @param string $value
	 * @return string
	 */
	private function array_prop ($property, $value) {
		return "'{$property}' => {$this->nice_value($value)}";
	}

	/**
	 * @param string $value
	 * @return string
	 */
	private function nice_value ($value) {
		$sub = substr($value, 1, strlen($value) - 2);

		switch ($value) {
			case "'true'":
			case '"true"':
				return "true";

			case "'false'":
			case '"false"':
				return "false";
		}

		switch (true) {
			case is_numeric($sub):
				return $sub;
		}

		if ($sub[0] === '{' && $sub[ strlen($sub) - 1 ] === '}') {
			return substr($sub, 1, strlen($sub) - 2);
		}

		return $value;
	}

	/**
	 * returns merge field content (no quotes)
	 * @param string $value
	 * @return string
	 */
	private function real_nice_value ($value) {
		return preg_replace([
			'/^"/', "/^'/", '/"$/', "/'$/",
		], '', $value);
	}

	/**
	 * @param string $value
	 * @param string $holder
	 * @return string
	 */
	private function parse_value ($value, $holder = MergeToken::IN_STR) {
		static $parser, $lexer;

		if (!$parser || !$lexer) {
			$parser = new Parser;
			$lexer = new Lexer;
		}

		$lexer->add_token(new MergeToken);
		$lexer->add_token(new FunctionToken);
		$lexer->set_string(trim($value));

		// save
		$m_orig = MergeToken::$holder;
		$f_orig = FunctionToken::$holder;

		// overwrite
		MergeToken::$holder = $holder;
		FunctionToken::$holder = $holder;

		// parse
		$value = $parser->parse($lexer);

		// reset
		MergeToken::$holder = $m_orig;
		FunctionToken::$holder = $f_orig;

		return $value;
	}
}
