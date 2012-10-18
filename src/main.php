<?php

namespace fabrico;

require 'core/core.php';
require 'core/module.php';
require 'core/util.php';
require 'loader/loader.php';
require 'loader/core.php';
require 'loader/deps.php';

use fabrico\core\util;
use fabrico\core\Core;
use fabrico\core\Reader;
use fabrico\core\Router;
use fabrico\core\EventDispatch;
use fabrico\loader\CoreLoader;
use fabrico\loader\DepsLoader;
use fabrico\configuration\Configuration;
use fabrico\configuration\ConfigurationItem;
use fabrico\configuration\ConfigurationItems;

// loaders
Core::instance()->core = new CoreLoader;
Core::instance()->deps = new DepsLoader;
Core::instance()->deps->set_path('../../../admin/php_include/');

Core::instance()->core->load('core');
Core::instance()->core->load('configuration');
Core::instance()->core->load('error');

// prepare the configuration's reader
// Core::instance()->deps->load('yml');
Reader::set_yml(function ($file) {
	return \sfYaml::load($file);
});

// initialize core modules
Core::instance()->reader = new Reader;
Core::instance()->event = new EventDispatch;
Core::instance()->router = new Router($_REQUEST);
Core::instance()->configuration = new Configuration;
Core::instance()->configuration->load('../configuration/httpconf.yml', Configuration::APC);









Core::instance()->router->route();

Core::instance()->core->load('page');

$cm = new Configuration;
$ci = new ConfigurationItems;
$c1 = new ConfigurationItem([
	'templates' => '/templates/'
]);

$cm->set('core', $ci);
$ci->set('dirs', $c1);

util::dpre($cm);
util::dpre($cm->core->dirs->templates);
