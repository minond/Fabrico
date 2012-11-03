<?php

namespace fabrico;

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

require '../core/core.php';
require '../core/module.php';
require '../core/util.php';
require '../loader/loader.php';
require '../loader/core.php';
require '../loader/deps.php';

// loaders
core::instance()->core = new CoreLoader;
core::instance()->deps = new DepsLoader;
require core::DEPS_INITIALIZER;

// framework configuration
// initialize core modules
core::instance()->configuration = new Configuration;
core::instance()->configuration->load(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);
require core::CORE_INITIALIZER;

// route the request
switch (true) {
	case core::instance()->router->is_view:
		core::instance()->core->load('page');
		require core::VIEW_INITIALIZER;
		break;
	
	default:
		core::instance()->response->addheader(Response::HTTP404);
		break;
}

core::instance()->response->reply();
