<?php

class FabricoURL {
	const MATCH_ID = '/(.*)\/(\d+?)$/';

	private static $matching;
	private static $expects;
	private static $updates;

	public static function matching ($rexex) {
		self::$matching = $rexex;
	}

	public static function expects ($num) {
		self::$expects = $num;;
	}

	public static function updates ($list) {
		self::$updates = $list;
	}

	public static function run () {
		preg_match(self::$matching,
			Fabrico::req(Fabrico::$uri_query_file),
		$matches);

		if (count($matches) >= self::$expects + 1) {
			for ($i = 1, $max = count(self::$updates); $i <= $max; $i++) {
				Fabrico::$req[ self::$updates[ $i - 1 ] ] = $matches[ $i ];
			}
		}
	}
}
