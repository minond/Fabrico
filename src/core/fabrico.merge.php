<?php

namespace Fabrico;

class Merge {
	/**
	 * merge field selector
	 */
	const SELECTOR = '/\\#\{\w+?\}/';

	/**
	 * iteration limit
	 */
	const MAX_ITERATIONS = 100;

	/**
	 * parses a string for merge fields
	 *
	 * @param string raw
	 * @return array of merge fields
	 */
	private static function get_merge_fields ($string) {
		$lastpos = 0;
		$mergefields = [];

		for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
			preg_match(self::SELECTOR, $string, $matches, PREG_OFFSET_CAPTURE, $lastpos);

			if (!count($matches)) {
				break;
			}

			$lastpos = $matches[0][1] + 1;
			$mergefields[] = $matches[0][0];
		}

		return $mergefields;
	}

	/**
	 * cleans up a merge fields and returns its name
	 *
	 * @param string raw merge field
	 * @return string merge field name
	 */
	private static function get_merge_field ($raw) {
		return str_replace([ '#{', '}' ], '', $raw);
	}

	/**
	 * parses a string and merges in merge fields
	 *
	 * @param string raw string
	 * @param array of merge fields
	 * @return string merged string
	 */
	public static function parse ($string, $mergevalues) {
		$mergefields = self::get_merge_fields($string);

		foreach ($mergefields as $field) {
			$cfield = self::get_merge_field($field);
			$value = array_key_exists($cfield, $mergevalues) ?
			         $mergevalues[ $cfield ] : '';

			$string = str_replace($field, $value, $string);
		}

		return $string;
	}

	/**
	 * replaces merge fields with place holder merge fields
	 * useful for turning merge fields into php output tags
	 *
	 * @param string
	 * @param callable merge field place holder generator
	 * @return string
	 */
	public static function placeholder ($string, $maker) {
		$mergefields = self::get_merge_fields($string);

		foreach ($mergefields as $field) {
			$string = str_replace(
				$field,
				$maker(self::get_merge_field($field)),
				$string
			);
		}

		return $string;
	}

	/**
	 * helper for generating php output tags
	 *
	 * @param string with merge fields
	 * @return string with php fields
	 */
	public static function output_placeholder ($string) {
		return self::placeholder($string, function ($field) {
			return "<?= $$field ?>";
		});
	}
}
