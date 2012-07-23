#!/usr/bin/php -q

<?php

define('EXITCOMMAND', 'exit');
define('GOODBYE', PHP_EOL . 'goodbye' . PHP_EOL . PHP_EOL);
define('PROMPT', PHP_EOL . 'fabrico> ');
define('CLEAR', 'clear');

while (true) {
	echo PROMPT;
	$input = trim(fread(STDIN, 80));
	$parts = explode(' ', $input);
	$command = $parts[ 0 ];
	$args = '';

	if (count($parts) > 1) {
		list(, $args) = explode(' ', $input, 2);
	}

	action_command($command, $args) or standard_command($command);
}

function loadproject ($argv) {
	$project = $argv[ 0 ];

	echo 'loading core files...' . PHP_EOL;
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
	require_once 'Fabrico.model.php';
	echo 'core files loaded' . PHP_EOL;

	$req = array(
		Fabrico::$uri_query_file => 'cli'
	);

	Fabrico::initialize($req);

	if ($project) {
		echo "loading $project..." . PHP_EOL;
		echo 'load complete' . PHP_EOL;
	}
	else {
		echo 'please enter a valid project name' . PHP_EOL;
	}
}

function action_command ($command, $args) {
	$args = explode(' ', $args);

	switch ($command) {
		case 'load':
			loadproject($args);
			return true;

		default:
			return false;
	}
}

function standard_command ($command) {
	switch ($command) {
		case EXITCOMMAND:
			echo GOODBYE;
			exit(0);

		case CLEAR:
			for ($i = 0; $i < 100; $i++)
				echo PHP_EOL;
			break;

		default:
			try {
				eval($input);
			}
			catch (Exception $error) {
				print_r($error);
			}

			break;
	}
}
