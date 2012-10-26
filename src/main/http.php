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

// initialize core modules
core::instance()->project = new Project;
core::instance()->reader = new Reader;
core::instance()->event = new EventDispatch;
core::instance()->router = new Router($_REQUEST);
core::instance()->configuration = new Configuration;
core::instance()->configuration->load('core', '../configuration/httpconf.yml', Configuration::APC);

core::instance()->core->load('page');
$page = new Page;
$page->title = "this is my title";
$page->content = "this is my content!";
echo $page->render();
die;

// route the request
switch (true) {
	case core::instance()->router->is_view:
		core::instance()->core->load('page');
		core::instance()->page = new Page;
		core::instance()->page->view = new View;
		core::instance()->page->view->builder = new Build;
		core::instance()->page->view->dispatch('index', Project::VIEW);
		break;
	
	default:
		break;
}
