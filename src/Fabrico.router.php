<?php

require_once 'Fabrico.main.php';
require_once 'Fabrico.util.php';

Fabrico::$req =& $_REQUEST;
Fabrico::check_debugging();
Fabrico::timer_start();

// valid view file
if (Fabrico::init() && !Fabrico::is_internal()) {
	require_once 'Fabrico.controller.php';
	require_once 'Fabrico.response.php';
	require_once 'Fabrico.model.php';

	// regular page request
	if (Fabrico::is_view_request()) {
		require_once 'Fabrico.html.php';
		require_once 'Fabrico.element.php';
		require_once 'Fabrico.template.php';

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
		Fabrico::timer_stop();
		Fabrico::timer_log();
		Fabrico::redirect();
	}
	
	Fabrico::timer_stop();
	Fabrico::timer_log();
}
else {
	Fabrico::timer_stop();
	Fabrico::timer_log();
	Fabrico::redirect();
}
