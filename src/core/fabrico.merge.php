<?php

namespace Fabrico;

class Merge {
	/**
	 * merge field selector
	 */
	const SELECTOR = '/\\#\{.+?\}/';

	/**
	 * iteration limit
	 */
	const MAX_ITERATIONS = 100;

	/**
	 * parses a string and merges in merge fields
	 *
	 * @param string raw string
	 * @param array of merge fields
	 * @return string merged string
	 */
	public static function parse ($string, $mergevalues) {
		$lastpos = 0;
		$mergefields = array();

		for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
			preg_match(self::SELECTOR, $string, $matches, PREG_OFFSET_CAPTURE, $lastpos);

			if (!count($matches)) {
				break;
			}

			$lastpos = $matches[0][1] + 1;
			$mergefields[] = $matches[0][0];
		}

		foreach ($mergefields as $field) {
			$cfield = str_replace(array('#{', '}'), '', $field);
			$value = array_key_exists($cfield, $mergevalues) ?
			         $mergevalues[ $cfield ] : '';

			$string = str_replace($field, $value, $string);
		}

		return $string;
	}
}
