<?php

/**
 * standard handling of http requests
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\output\Page;
use fabrico\output\Json;
use fabrico\output\View;
use fabrico\output\Build;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\core\Response;
use fabrico\controller\Controller;
use fabrico\configuration\RoutingRule;

require 'main.php';

Core::run(function (Core $app) {
	// load route
	$conf = $app->configuration;
	$conf->load('routes', '../configuration/routes.json', new RoutingRule);

	// apply routing rules
	foreach ($conf->routes as $route) {
		if ($route->try_reading($_SERVER['REQUEST_URI'], $_REQUEST)) {
			break;
		}
	}

	// request handlers
	$app->request = $request = new Request;
	$app->router = $router = new Router($_REQUEST);
	$app->response = $response = new Response;

	// route the request
	if ($router->is_view) {
		// load page related modules and initialize them
		$app->loader->load('output');
		$app->loader->load('page');

		// add page module to the response, view and build
		$response->outputcontent = new Page;
		$response->outputcontent->view = new View;
		$response->outputcontent->view->builder = new Build;

		// load the view file
		$response->outputcontent->load($request->get_file());
	}
	else if ($router->is_method) {
		$app->loader->load('output');
		$app->loader->load('controller');

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
