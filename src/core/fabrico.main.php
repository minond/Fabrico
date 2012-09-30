<?php

namespace fabrico;

require 'fabrico.core.php';
require 'fabrico.loader.php';
require 'fabrico.loader.core.php';

// load core
Core::instance()->loader = new CoreLoader;
Core::instance()->loader->load();

// initialize modules
Core::instance()->router = new Router($_REQUEST);
Core::instance()->event = new EventDispatch;
