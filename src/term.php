<?php

/**
 * cli handler
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\core\Project;
use fabrico\controller\Controller;
use fabrico\Controller\CliAccess;

require 'main.php';

Core::run(function (Core $app) {
	global $argv, $argc;
	$app->loader->load('cli');
	$app->loader->load('controller');

	list(, $request) = explode('=', $argv[1]);
	list($controller, $method) = explode(':', $request);
	$controller = ucwords($controller);

	list(, $found) = $app->project->got_file($controller, Project::CONTROLLER);

	if ($found) {
		$controller = Controller::load($controller);

		if ($controller instanceof CliAccess) {
			$controller->load_cli_arguments();
			Controller::trigger_cli_request($controller, $method);
		}
		else {
			printf('Controller "%s" in not public%s', get_class($controller), PHP_EOL);
		}
	}
	else {
		printf('Controller "%s" was not found%s', $controller, PHP_EOL);
	}
});
