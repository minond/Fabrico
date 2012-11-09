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
				$propmap[ trim($prop) ] = $this->parse_value($vals[ $index ]);
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
				$props[] = array_pop($strparts);
				$vals[] = implode(' ', $strparts);
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

		return $value;
	}

	/** 
	 * @param string $value
	 * @return string
	 */
	private function parse_value ($value) {
		$parser = new Parser;
		$lexer = new Lexer;
		$lexer->add_token(new MergeToken);
		$lexer->set_string(trim($value));

		$orig = MergeToken::$holder;
		MergeToken::$holder = MergeToken::IN_STR;
		$value = $parser->parse($lexer);
		MergeToken::$holder = $orig;

		return $value;
	}
}
