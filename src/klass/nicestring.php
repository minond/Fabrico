<?php

/**
 * @package fabrico\klass
 */
namespace fabrico\klass;

/**
 * string functions. used to work with properties
 */
trait NiceString {
	/**
	 * compares two strings using soundex keys
	 * @param string $s1
	 * @param string $s2
	 * @return boolean
	 */
	private function sounds_like($s1, $s2) {
		return soundex($s1) === soundex($s2);
	}

	/**
	 * converts a property name into a human friendly string
	 * @param string $prop
	 * @return string
	 */
	private function prop2human($prop) {
		$words = explode('_', $prop);
		$lastcaps = 0;

		if (!count($words)) {
			$min = 65;
			$max = 90;

			for ($i = 0, $m = strlen($prop); $i < $m; $i++) {
				$ord = ord($prop[ $i ]);

				if ($ord >= $min && $ord <= $max) {
					$words[] = trim(substr($prop, $lastcaps, $i));
					$lastcaps = $i;
				}
			}
		}

		return implode(' ', $words);
	}
}
