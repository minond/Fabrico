<?php

/**
 * standard handling of http requests
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\core\Project;
use fabrico\core\EventDispatch;
use fabrico\loader\CoreLoader;
use fabrico\cache\RuntimeMemory;
use fabrico\cache\Apc;
use fabrico\configuration\StandardItem;
use fabrico\configuration\RoutingRule;
use fabrico\configuration\ConfigurationManager;

require 'core/core.php';

Core::run(function (Core $app) {
	$app->loader = new CoreLoader;

	// base modules and configuration
	$app->configuration = $conf = new ConfigurationManager(new RuntimeMemory);
	$conf->load('core', '../configuration/httpconf.json', new StandardItem);
	$app->event = new EventDispatch;
	$app->project = new Project(
		$conf->core->project->name,
		$conf->core->project->path,
		$conf->core->project->webroot
	);
});
