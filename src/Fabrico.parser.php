<?php

class FabricoParser {
	/**
	 * character used to identify a merge field
	 *
	 * @var string
	 */
	public static $pchar = '%';

	/**
	 * parses a string and merges in data from passed variable
	 *
	 * @param string $str
	 * @param object $data
	 * @return string
	 */
	public static function merge ($str, $data) {
		$data = is_array($data) ? (object) $data : $data;
		
		util::cout($data);
		foreach ($data as $merge => $value) {
			$str = str_replace(self::$pchar . $merge, $value, $str);
		}

		return $str;
	}
}
