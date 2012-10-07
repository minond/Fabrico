<?php

namespace fabrico;

require 'core.php';
require 'module.php';
require 'loader.php';
require 'loader.core.php';

// loaders
Core::instance()->core = new CoreLoader;
Core::instance()->core->load();
Core::instance()->deps = new DepsLoader;
Core::instance()->deps->conf_dep_file('s');

// initialize modules
Core::instance()->router = new Router($_REQUEST);
Core::instance()->event = new EventDispatch;
Core::instance()->project = new ProjectManager;
