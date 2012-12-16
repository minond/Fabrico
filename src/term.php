<?php

/**
 * cli handler
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\project\Project;
use fabrico\controller\Controller;
use fabrico\Controller\CliAccess;

require 'main.php';

Core::run(function (Core $app) {
	global $argv, $argc;
	$opts = getopt('', array('request:'));

	if ($argc > 1 && isset($opts['request']) && strlen($opts['request'])) {
		$app->loader->load('cli');
		$app->loader->load('controller');

		if (strpos($argv[1], ':') !== false && strpos($argv[1], '=') !== false) {
			list(, $request) = explode('=', $argv[1]);
			list($controller_name, $method) = explode(':', $request);
			$controller_name = ucwords($controller_name);
			$controller = Controller::load($controller_name);

			if ($controller) {
				if ($controller instanceof CliAccess) {
					$controller->load_cli_arguments();
					Controller::trigger_cli_request($controller, $method);
				}
				else {
					printf('Controller "%s" in not public%s', get_class($controller), PHP_EOL);
				}
			}
			else {
				printf('Controller "%s" was not found%s', $controller_name, PHP_EOL);
			}
		}
		else {
			printf('Invalid request: "%s"%s', $argv[1], PHP_EOL);
		}
	}
	else {
		printf('Request required%s', PHP_EOL);
	}
});
