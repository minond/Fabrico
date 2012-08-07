<?php

namespace Fabrico;

class Core {
	/**
	 * core dependancies
	 *
	 * @var array
	 */
	public static $deps = array(
		'../deps/sfYaml/sfYaml.php'
	);

	/**
	 * path to framework configuration
	 */
	public static $configuration_path = '../../configuration/configuration.yml';

	/**
	 * holds framework and project configuration
	 *
	 * @var object
	 */
	public static $configuration;

	/**
	 * loads framework configuration
	 */
	public static function load_core_configuration () {
		self::$configuration = (object) \sfYaml::load(self::$configuration_path);
		self::$configuration->state = new \stdClass();
		self::$configuration->loading = (object) self::$configuration->loading;
		self::$configuration->directory = (object) self::$configuration->directory;
		self::$configuration->convention = (object) self::$configuration->convention;
	}

	/**
	 * initializes needed variables and modules
	 */
	public static function load_core_setup () {
		Project::set_files();
	}

	/**
	 * loads core dependancies
	 */
	public static function load_core_dependancies () {
		foreach (self::$deps as $dep) {
			require_once $dep;
		}
	}
}
