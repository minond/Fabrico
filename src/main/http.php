<?php

namespace fabrico;

require '../core/core.php';
require '../core/module.php';
require '../core/util.php';
require '../loader/loader.php';
require '../loader/core.php';
require '../loader/deps.php';

use fabrico\core\util;
use fabrico\core\core;
use fabrico\core\Reader;
use fabrico\core\Router;
use fabrico\core\Request;
use fabrico\core\Response;
use fabrico\core\Project;
use fabrico\core\EventDispatch;
use fabrico\page\Page;
use fabrico\page\View;
use fabrico\page\Build;
use fabrico\loader\CoreLoader;
use fabrico\loader\DepsLoader;
use fabrico\configuration\Configuration;
use fabrico\configuration\ConfigurationItem;
use fabrico\configuration\ConfigurationItems;
use fabrico\controller\Controller;

// loaders
core::instance()->core = new CoreLoader;
core::instance()->deps = new DepsLoader;
core::instance()->deps->set_path('../../../admin/php_include/');

core::instance()->core->load('core');
core::instance()->core->load('configuration');
core::instance()->core->load('error');

// prepare the configuration's reader
Reader::set_yml(function ($file) {
	return \sfYaml::load($file);
});

// framework configuration
core::instance()->configuration = new Configuration;
core::instance()->configuration->load('core', '../../configuration/httpconf.yml', Configuration::APC);

// project configuration
// ...

// initialize core modules
core::instance()->project = new Project;
core::instance()->reader = new Reader;
core::instance()->event = new EventDispatch;
core::instance()->request = new Request;
core::instance()->router = new Router($_REQUEST);
core::instance()->response = new Response;
core::instance()->response->as = Response::HTML;

// route the request
switch (true) {
	case core::instance()->router->is_view:
		require 'view.php';
		break;
	
	default:
		core::instance()->response->addheader('HTTP/1.0 404 Not Found');
		break;
}

core::instance()->response->reply();
