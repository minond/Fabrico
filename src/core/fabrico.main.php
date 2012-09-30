<?php

namespace fabrico;

require 'fabrico.core.php';
require 'fabrico.autoload.php';
require 'fabrico.loader.php';

// load everything
Core::instance()->loader = new FabricoLoader;
Core::instance()->loader->load('core');

// start buiding the parts
Core::instance()->router = new Router;
