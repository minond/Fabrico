<?php

if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
	die;
}

require_once 'fabrico.utils.php';
require_once 'fabrico.log.php';
require_once 'fabrico.core.php';
require_once 'fabrico.merge.php';
require_once 'fabrico.router.php';
require_once 'fabrico.project.php';
require_once 'fabrico.controller.php';
require_once 'fabrico.views.php';
require_once 'fabrico.page.php';
require_once 'fabrico.tag.php';
require_once 'fabrico.response.php';
require_once 'fabrico.build.php';
require_once 'fabrico.element.php';
require_once 'fabrico.template.php';
require_once 'fabrico.error.php';

Fabrico\Core::load_core_dependancies();
Fabrico\Core::load_core_configuration();
Fabrico\Core::load_core_setup($_REQUEST);
Fabrico\Core::load_project_configuration();
Fabrico\Core::start_active_record();
Fabrico\Core::handle_request();
