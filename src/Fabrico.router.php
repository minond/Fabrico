<?php

require_once 'Fabrico.state.php';
require_once 'Fabrico.util.php';
require_once 'Fabrico.main.php';
require_once 'Fabrico.html.php';
require_once 'Fabrico.connection.php';
require_once 'Fabrico.controller.php';
require_once 'Fabrico.response.php';

Fabrico::check_debugging();

// valid view file
if (Fabrico::init($_REQUEST) && !Fabrico::is_internal()) {
	// regular page request
	if (Fabrico::is_view_request()) {
		Fabrico::init_template();
	}
	// controller method call request
	else if (Fabrico::is_method_request()) {
		Fabrico::init_method();
	}
	// action method call request
	else if (Fabrico::is_action_request()) {
		Fabrico::init_action();
	}
	// unknown
	else {
		Fabrico::redirect();
	}
}
else {
	Fabrico::redirect();
}

