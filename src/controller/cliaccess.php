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
	public function load_cli_arguments();
}
