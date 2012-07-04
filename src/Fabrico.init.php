<?php

require_once 'Fabrico.constants.php';
require_once 'Fabrico.util.php';
require_once 'Fabrico.main.php';
require_once 'Fabrico.html.php';
require_once 'Fabrico.connection.php';

if (Fabrico::init()) {
	require Fabrico::get_controller_file();
	require Fabrico::get_requested_file();
}
else {
	Fabrico::redirect();
}
