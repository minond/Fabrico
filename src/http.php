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
use fabrico\status\ControllerStatus;
use fabrico\controller\Controller;
use fabrico\controller\WebAccess;
use fabrico\configuration\RoutingRule;
use fabrico\project\Project;

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
		$app->loader->load('output', 'klass', 'model', 'page');

		// add page module to the response, view and build
		$response->outputcontent = new Page;
		$response->outputcontent->view = new View;
		$response->outputcontent->view->builder = new Build;

		// load the view file
		if ($app->project->has_file($request->get_file(), Project::VIEW)) {
			$response->outputcontent->load($request->get_file());
		}
		else {
			$response->outputcontent->load('404');
		}
	}
	else if ($router->is_method) {
		$app->loader->load('output', 'klass', 'model', 'controller');

		$controller = $request->get(Router::$var->controller);
		$controller = Controller::load($controller, false);
		$response->outputcontent = new Json;
		$response->outputcontent->status = null;
		$response->outputcontent->return = null;

		if (is_null($controller)) {
			$response->outputcontent->status = ControllerStatus::UNKNOWN_C;
		}
		else if ($controller instanceof WebAccess) {
			if ($controller->published($request->get(Router::$var->method))) {
				$response->outputcontent->status = ControllerStatus::OK;
				$response->outputcontent->return =
					Controller::trigger_web_request($controller, $request);
			}
			else {
				$response->outputcontent->status = ControllerStatus::PRIVATE_M;
			}
		}
		else {
			$response->outputcontent->status = ControllerStatus::PRIVATE_C;
		}
	}
	else {
		$response->addheader(Response::HTTP404);
	}

	$response->send();
});
