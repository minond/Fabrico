<?php

namespace Fabrico;

class Format {
	/**
	 * format types
	 */
	const F_DEFAULT = 'string';
	const F_STRING = 'string';
	const F_NUMBER = 'number';
	const F_DATE = 'date';

	/**
	 * formats a string
	 *
	 * @param mixed raw value
	 * @param string format type
	 * @param string format string
	 * @return string formatted string
	 */
	public static function format ($raw, $type, $format) {
		switch ($type) {
			case self::F_STRING:
			case self::F_NUMBER:
				return $raw;

			case self::F_DATE:
				return date($format, $raw);

			default:
				return 'Invalid format';
		}
	}
}
