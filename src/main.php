<?php

/**
 * standard handling of http requests
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\project\Project;
use fabrico\loader\CoreLoader;
use fabrico\cache\RuntimeMemory;
use fabrico\cache\Apc;
use fabrico\configuration\StandardItem;
use fabrico\configuration\RoutingRule;
use fabrico\configuration\ConfigurationManager;
use fabrico\logging\Logz;
use fabrico\logging\handler\StdErrHandler;

require 'core/core.php';

Core::run(function (Core $app) {
	$app->wd = getcwd();
	$app->loader = new CoreLoader;
	$app->log = new Logz;
	$app->log->add_handler(new StdErrHandler(Logz::INFO));
	$app->log('loader and logger created');

	// base modules and configuration
	$app->configuration = $conf = new ConfigurationManager(new RuntimeMemory);
	$conf->load('core', '../configuration/httpconf.json', new StandardItem);
	$app->log('configuration loaded');

	// project information
	$app->project = new Project;
	$app->project->set_project_name($conf->core->project->name);
	$app->project->set_root($conf->core->project->root);
	$app->project->set_fsroot($conf->core->project->fsroot);
	$app->project->set_webroot($conf->core->project->webroot);
	$app->project->set_myroot($conf->core->project->myroot);
	$app->project->set_mywebroot($conf->core->project->mywebroot);
	$app->log('project information loaded');
});
