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

	$opts = getopt('', [
		'controllername:',
		'methodname:',
		'workingdirectory:'
	]);

	if (!isset($opts['methodname'])) {
		$opts['methodname'] = 'trigger';
	}

	if (isset($opts['workingdirectory'])) {
		$app->wd = $opts['workingdirectory'];
	}

	if ($argc > 1 && isset($opts['controllername'])) {
		$app->loader->load('klass');
		$app->loader->load('cli');
		$app->loader->load('controller');

		$method = str_replace('-', '_', $opts['methodname']);
		$controller_name = ucwords($opts['controllername']);
		$controller = Controller::load($controller_name);

		if ($controller) {
			if ($controller instanceof CliAccess) {
				$controller->load_cli_property_arguments();
				$controller->load_cli_function_arguments($method);

				try {
					Controller::trigger_cli_request($controller, $method);
				} catch (\Exception $error) {
					printf($error->getMessage());
				}
			}
			else {
				printf(
					'Controller "%s" in not public%s',
					get_class($controller), PHP_EOL
				);
			}
		}
		else {
			printf(
				'Controller "%s" was not found%s',
				$controller_name, PHP_EOL
			);
		}
	}
	else {
		printf('Request required%s', PHP_EOL);
	}
});
