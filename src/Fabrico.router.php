<?php

if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
	die;
}

require_once 'Fabrico.main.php';
require_once 'Fabrico.error.php';
require_once 'Fabrico.util.php';
require_once 'Fabrico.url.php';
require_once 'Fabrico.html.php';
require_once 'Fabrico.element.php';
require_once 'Fabrico.template.php';
require_once 'Fabrico.page.php';
require_once 'Fabrico.controller.php';
require_once 'Fabrico.response.php';
require_once 'Fabrico.database.php';
require_once 'Fabrico.model.php';

Fabrico::initialize($_REQUEST);
Fabrico::handle_request(true);
