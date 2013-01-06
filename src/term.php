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

	$app->log('running as cli command');
	$app->log('loading base arguments');

	if (!isset($opts['methodname'])) {
		$opts['methodname'] = 'trigger';
	}

	if (isset($opts['workingdirectory'])) {
		$app->wd = $opts['workingdirectory'];
	}

	if ($argc > 1 && isset($opts['controllername'])) {
		$app->log('validated base arguments, loading cli classes');
		$app->loader->load('klass');
		$app->loader->load('cli');
		$app->loader->load('controller');

		$method = str_replace('-', '_', $opts['methodname']);
		$controller_name = ucwords($opts['controllername']);
		$controller = Controller::load($controller_name);
		$app->log('parsed controller and method strings');

		if ($controller) {
			$app->log('checking controller type');

			if ($controller instanceof CliAccess) {
				$app->log('parcing command arguments');
				$controller->load_cli_property_arguments();
				$controller->load_cli_function_arguments($method);

				try {
					$app->log('calling controller method');
					Controller::trigger_cli_request($controller, $method);
					$app->log('controller method called');
				} catch (\Exception $error) {
					$app->log('error calling controller method');
					printf($error->getMessage());
				}
			}
			else {
				$controller_class = get_class($controller);
				$app->log('invalid controller: ' . $controller_class);
				printf(
					'Controller "%s" in not public%s',
					$controller_class, PHP_EOL
				);
			}
		}
		else {
			$app->log('controller not found: ' . $controller_name);
			printf(
				'Controller "%s" was not found%s',
				$controller_name, PHP_EOL
			);
		}
	}
	else {
		$app->log('missing arguments');
		printf('Request required%s', PHP_EOL);
	}
});
