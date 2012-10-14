<?php

namespace Fabrico;

class Merge {
	/**
	 * merge field selector
	 */
	const SELECTOR = '/\\#\{.+?\}/';
	const PLACEHOLDER_SELECTOR = '/\{\\#.+?\}/';

	/**
	 * iteration limit
	 */
	const MAX_ITERATIONS = 100;

	/**
	 * parses a string for merge fields
	 *
	 * @param string raw
	 * @param boolean return clean merge fields
	 * @return array of merge fields
	 */
	public static function get_merge_fields ($string, $clean = false, $selector = self::SELECTOR) {
		$lastpos = 0;
		$mergefields = [];

		for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
			preg_match($selector, $string, $matches, PREG_OFFSET_CAPTURE, $lastpos);

			if (!count($matches)) {
				break;
			}

			$lastpos = $matches[0][1] + 1;
			$mergefields[] = $matches[0][0];
		}

		if ($clean) {
			foreach ($mergefields as $index => $field) {
				$mergefields[ $index ] = self::get_merge_field($field);
			}
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
		return preg_replace(
			[ '/!(\w)/', '/!/' ],
			[ '()->$1', '()' ],
			str_replace(
				[ '#{', '{#', '}', '.' ],
				[ '', '', '', '->' ],
				$raw
			)
		);
	}

	/**
	 * parses a string and merges in merge fields
	 *
	 * @param string raw string
	 * @param array of merge fields
	 * @return string merged string
	 */
	public static function parse ($string, $mergevalues, $selector = self::SELECTOR) {
		foreach (self::get_merge_fields($string, false, $selector) as $field) {
			$cfield = self::get_merge_field($field);

			if (is_array($mergevalues)) {
				$value = array_key_exists($cfield, $mergevalues) ?
				         $mergevalues[ $cfield ] : '';
			}
			else if (is_object($mergevalues)) {
				$value = isset($mergevalues->{ $cfield }) ?
				         $mergevalues->{ $cfield } : '';
			}

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
	 * adds a controller prefix to a merge field is
	 * it's part of the controller
	 *
	 * @param string $rawfield
	 * @return string
	 */
	private static function controller_prefix ($rawfield) {
		return '$_controller->' . $rawfield;
	}

	/**
	 * helper for generating php output tags
	 *
	 * @param string with merge fields
	 * @return string with php fields
	 */
	public static function output_controller_string_placeholder ($string) {
		return self::placeholder($string, function ($field) {
			return '{' . self::controller_prefix($field) . '}';
			// return '{$_controller->' . $field . '}';
		});
	}

	/**
	 * helper for generating php output tags
	 *
	 * @param string with merge fields
	 * @return string with php fields
	 */
	public static function output_string_placeholder ($string) {
		return self::placeholder($string, function ($field) {
			return '{$' . $field . '}';
		});
	}

	/**
	 * helper for generating php output tags
	 *
	 * @param string with merge fields
	 * @param boolean user string merge field
	 * @return string with php fields
	 */
	public static function output_controller_placeholder ($string, $in_string = false) {
		preg_match('/^"\\#\{.+?\}"$/', $string, $matches);

		if (count($matches)) {
			$string = self::get_merge_field($string);
			return self::controller_prefix(substr($string, 1, strlen($string) - 2));
			// return '$_controller->' . substr($string, 1, strlen($string) - 2);
		}

		if ($in_string) {
			return self::output_controller_string_placeholder($string);
		}
		else {
			return self::placeholder($string, function ($field) {
				return '<?= ' . self::controller_prefix($field) . ' ?>';
				return '<?= $_controller->' . $field . ' ?>';
			});
		}
	}

	/**
	 * helper for generating php output tags
	 *
	 * @param string with merge fields
	 * @return string with php fields
	 */
	public static function output_placeholder ($string) {
		return self::placeholder($string, function ($field) {
			return '<?= $' . $field . ' ?>';
		});
	}
}
