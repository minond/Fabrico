<?php

/**
 * @package fabrico\controller
 */
namespace fabrico\controller;

/**
 * acts as a Terminal access flag
 */
interface CliAccess {
	/**
	 * should load cli argument into class properties
	 * @see cli\CliArgLoader
	 */
	public function load_cli_property_arguments();

	/**
	 * @see cli\CliArgLoader
	 */
	public function load_cli_function_arguments($func);

	/**
	 * @see cli\CliArgLoader
	 */
	public function get_function_arguments();
}
