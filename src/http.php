<?php

namespace fabrico\core;

require 'core/core.php';
require 'core/module.php';
require 'core/util.php';
require 'loader/loader.php';
require 'loader/core.php';
require 'loader/deps.php';

use fabrico\loader\CoreLoader;
use fabrico\loader\DepsLoader;
use fabrico\core\Project;
use fabrico\core\Reader;
use fabrico\core\EventDispatch;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\core\Response;
use fabrico\configuration\Configuration;
use fabrico\output\Page;
use fabrico\output\View;
use fabrico\output\Build;

// loaders
core::instance()->core = new CoreLoader;
core::instance()->deps = new DepsLoader;
core::instance()->deps->set_path('../../admin/php_include/');

// base modules
core::instance()->project = new Project;
core::instance()->reader = new Reader;
core::instance()->event = new EventDispatch;

// load framework configuration
core::instance()->configuration = new Configuration;
//core::instance()->configuration->clear(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);
core::instance()->configuration->load(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);

// request handlers
core::instance()->request = new Request;
core::instance()->router = new Router($_REQUEST);
core::instance()->response = new Response;

/*
use fabrico\logz\Logz;
use fabrico\logz\handler\FileHandler;
core::instance()->core->load('log');

$log = new Logz('Testing');
$log->add_handler(new FileHandler(Logz::INFO, 'out.log'));
$log->information('hi');

util::dpre($log);
*/

// route the request
switch (true) {
	case core::instance()->router->is_view:
		// load page related modules and initialize them
		core::instance()->core->load('output');

		// add page module to the response, view and build
		core::instance()->response->outputcontent = new Page;
		core::instance()->response->outputcontent->view = new View;
		core::instance()->response->outputcontent->view->builder = new Build;

		// load the view file
		core::instance()->response->outputcontent->get(core::instance()->request->file);
		break;
	
	default:
		core::instance()->response->addheader(\fabrico\core\Response::HTTP404);
		break;
}

core::instance()->response->reply();
