<?php

require_once 'fabrico.core.php';
require_once 'fabrico.router.php';
require_once 'fabrico.project.php';

Fabrico\Core::load_core_dependancies();
Fabrico\Core::load_core_configuration();
Fabrico\Router::set_request($_REQUEST);
Fabrico\Core::load_core_setup();


// echo "<pre>"; print_r(Fabrico\Router::request_method());
// echo "<pre>"; print_r(Fabrico\Router::$uri);
// echo "<pre>"; print_r(Fabrico\Core::$configuration);
echo "<pre>"; print_r(Fabrico\Core::$configuration);
