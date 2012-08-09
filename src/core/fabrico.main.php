<?php

require_once 'fabrico.core.php';
require_once 'fabrico.router.php';
require_once 'fabrico.project.php';
require_once 'fabrico.controller.php';
require_once 'fabrico.views.php';
require_once 'fabrico.page.php';
require_once 'fabrico.response.php';

Fabrico\Core::load_core_dependancies();
Fabrico\Core::load_core_configuration();
Fabrico\Core::load_core_setup($_REQUEST);
Fabrico\Core::handle_request();
