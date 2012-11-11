<?php

/**
 * standard handling of http requests
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\core\Project;
use fabrico\core\Reader;
use fabrico\core\EventDispatch;
use fabrico\output\Page;
use fabrico\output\View;
use fabrico\output\Build;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\core\Response;
use fabrico\loader\CoreLoader;
use fabrico\loader\DepsLoader;
use fabrico\configuration\Configuration;

require 'core/core.php';

Core::run(function (Core $app) {
	// load core mods
	require_once 'core/module.php';
	require_once 'core/util.php';
	require_once 'loader/loader.php';
	require_once 'loader/core.php';
	require_once 'loader/deps.php';

	// loaders
	$app->core = new CoreLoader;
	$app->deps = new DepsLoader;
	$app->deps->set_path('../../admin/php_include/');

	// request handlers
	$app->request = $request = new Request;
	$app->router = $router = new Router($_REQUEST);
	$app->response = $response = new Response;

	// base modules and configuration 
	$app->project = new Project;
	$app->reader = new Reader;
	$app->event = new EventDispatch;
	$app->configuration = new Configuration;
	$app->configuration->load(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);

	// route the request
	switch (true) {
		case $router->is_view:
			// load page related modules and initialize them
			$app->core->load('output');

			// add page module to the response, view and build
			$response->outputcontent = new Page;
			$response->outputcontent->view = new View;
			$response->outputcontent->view->builder = new Build;

			// load the view file
			$response->outputcontent->load($request->file);
			break;

		default:
			$response->addheader(Response::HTTP404);
			break;
	}

	$response->send();
});
