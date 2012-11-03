<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

use fabrico\loader\DepsLoader;

/**
 * file reader
 */
class Reader extends Module {
	/**
	 * yml file format reader
	 * @var callable
	 */
	private static $yml;

	/**
	 * @param callable $reader
	 */
	public static function set_yml (callable $reader) {
		self::$yml = $reader;
	}

	/**
	 * @param string $file
	 */
	public function yml ($file) {
		$this->getc()->deps->load(DepsLoader::YML);
		return call_user_func(self::$yml, $file);
	}
}

Reader::set_yml(function ($file) {
	return \sfYaml::load($file);
});
