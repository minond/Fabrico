<?php

namespace fabrico;

require 'core.php';
require 'module.php';
require 'util.php';
require 'loader.php';
require 'loader.core.php';
require 'loader.deps.php';

// loaders
Core::instance()->core = new CoreLoader;
Core::instance()->deps = new DepsLoader;
Core::instance()->deps->set_path('../../../admin/php_include/');

Core::instance()->core->load();

// prepare the configuration's reader
// Core::instance()->deps->load('yml');
Reader::yml(function ($file) {
	return \sfYaml::load($file);
});

// initialize core modules
Core::instance()->event = new EventDispatch;
Core::instance()->router = new Router($_REQUEST);
Core::instance()->router->request($_REQUEST);
Core::instance()->router->route();

// project loader
// Core::instance()->project = new ProjectManager;
// Core::instance()->project->configuration = new ConfigurationManager;

$cm = new ConfigurationManager;
$ci = new ConfigurationItems;
$c1 = new ConfigurationItem([
	'templates' => '/templates/'
]);

$cm->set('core', $ci);
$ci->set('dirs', $c1);

util::dpre($cm);
util::dpre($cm->core->dirs->templates);
