<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

/**
 * tag property token
 */
class PropertyToken extends Token {
	/**
	 * @see Token::parse
	 */
	public function parse (array $match) {
		$prop = trim($match[ 0 ]);
		$parts = explode('=', $prop);
		$lastprop = count($parts) - 1;
		$propstrs = [];
		$propmap = [];
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

		foreach ($props as $index => $prop) {
			if (isset($vals[ $index ])) {
				$propmap[ trim($prop) ] = trim($vals[ $index ]);
			}
		}

		foreach ($propmap as $property => $value) {
			$propstrs[] = "'{$property}' => {$value}";
		}

		$this->replacement = implode(', ', $propstrs);
		$this->string = trim($match[ 0 ]);
		$this->valid = true;
	}
}
