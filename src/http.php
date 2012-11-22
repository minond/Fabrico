<?php

/**
 * standard handling of http requests
 */
namespace fabrico;

use fabrico\core\util;
use fabrico\core\Core;
use fabrico\core\Project;
use fabrico\core\Reader;
use fabrico\core\EventDispatch;
use fabrico\output\Page;
use fabrico\output\Json;
use fabrico\output\View;
use fabrico\output\Build;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\core\Response;
use fabrico\controller\Controller;
use fabrico\loader\CoreLoader;
use fabrico\cache\RuntimeMemory;
use fabrico\cache\Apc;
use fabrico\configuration\StandardItem;
use fabrico\configuration\RoutingRule;
use fabrico\configuration\ConfigurationManager;

require 'core/core.php';

Core::run(function (Core $app) {
	$app->core = new CoreLoader;

	// request handlers
	$app->request = $request = new Request;
	$app->router = $router = new Router($_REQUEST);
	$app->response = $response = new Response;

	// base modules and configuration 
	$app->configuration = $conf = new ConfigurationManager(new RuntimeMemory);
	$conf->load('core', '../configuration/httpconf.json', new StandardItem);
	$conf->load('routes', '../configuration/routes.json', new RoutingRule);

	$app->project = new Project($conf->core->project->name, $conf->core->project->path, '/'.$conf->core->project->name);
	$app->event = new EventDispatch;

	// route the request
	if ($router->is_view) {
		// load page related modules and initialize them
		$app->core->load('output');
		$app->core->load('page');

		// add page module to the response, view and build
		$response->outputcontent = new Page;
		$response->outputcontent->view = new View;
		$response->outputcontent->view->builder = new Build;

		// load the view file
		$response->outputcontent->load($request->get_file());
	}
	else if ($router->is_method) {
		$app->core->load('output');
		$app->core->load('controller');

		$controller = Controller::req_load($request);
		$response->outputcontent = new Json;
		$response->outputcontent->status = Controller::request_status($controller, $request);
		$response->outputcontent->return = Controller::trigger_method($controller, $request);
	}
	else {
		$response->addheader(Response::HTTP404);
	}

	$response->send();
});
