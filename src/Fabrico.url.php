<?php

class FabricoURL {
	/**
	 * @name MATCH_ID
	 * @constant string
	 */
	const MATCH_ID = '/(.*)\/(\d+?)\/?$/';

	/**
	 * @name MATCH_PROJECT
	 * @constant string
	 */
	const MATCH_PROJECT = '/^\[(.+)\]\/(.+)$/';

	/**
	 * @name matching
	 * @var array
	 */
	private static $matching = array();

	/**
	 * @name expects
	 * @var array
	 */
	private static $expects = array();

	/**
	 * @name updates
	 * @var array
	 */
	private static $updates = array();

	/**
	 * @name rule
	 * @param int number of matched required
	 * @param string regular expressions to run request against
	 * @param array of request keys to update
	 */
	public static function rule ($num, $rexex, $list) {
		self::$expects[] = $num;;
		self::$matching[] = $rexex;
		self::$updates[] = $list;
	}

	/**
	 * @name run
	 * runs all created rules
	 */
	public static function run () {
		for ($j = 0, $max_rules = count(self::$matching); $j < $max_rules; $j++) {
			preg_match(self::$matching[ $j ],
				Fabrico::req(Fabrico::$uri_query_file),
			$matches);

			if (count($matches) >= self::$expects[ $j ] + 1) {
				for ($i = 1, $max = count(self::$updates[ $j ]); $i <= $max; $i++) {
					Fabrico::$req[ self::$updates[ $j ][ $i - 1 ] ] = $matches[ $i ];
				}
			}
		}
	}

	/**
	 * @name project
	 * @param stdClass configuration/settings reference
	 */
	public static function project (& $settings) {
		preg_match(
			self::MATCH_PROJECT,
			Fabrico::req(Fabrico::$uri_query_file),
			$matches
		);

		if (count($matches) === 3) {
			$settings->loading->path = $matches[1];
			Fabrico::$req[ Fabrico::$uri_query_file ] = $matches[2];
		}
	}
}
