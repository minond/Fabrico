#!/usr/bin/php -q

<?php

define('EOL', PHP_EOL);
define('EOS', ';');
define('EXITCOMMAND', 'exit');
define('QUITCOMMAND', 'quit');
define('GOODBYE', EOL . 'goodbye' . EOL . EOL);
define('PROMPT', EOL . 'fabrico> ');
define('PROMPTCONT', '       > ');
define('CLEAR', 'clear');

$textbuffer = '';
$runsbuffer = false;

while (true) {
	echo $runsbuffer ? PROMPTCONT : PROMPT;
	$input = trim(fread(STDIN, 80));
	$parts = explode(' ', $input);
	$command = $parts[ 0 ];
	$args = '';

	if (count($parts) > 1) {
		list(, $args) = explode(' ', $input, 2);
	}

	$args = $textbuffer . $args;

	handlecommand($command, $args);
}

function loadproject ($argv) {
	$project = $argv[ 0 ];

	if (!$project) {
		echo 'please enter a valid project name' . EOL;
		return;
	}

	echo 'loading core files...' . EOL;
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
	echo 'core files loaded' . EOL;

	echo 'initializing Fabrico...' . EOL;
	$req = array(
		Fabrico::$uri_query_file => 'cli'
	);

	Fabrico::$file_config = 'config/cli_config.ini';
	Fabrico::initialize($req);
	echo "$project project loaded" . EOL;
}

function handlecommand ($command, $args) {
	$argv = explode(' ', $args);

	switch ($command) {
		case EXITCOMMAND:
		case QUITCOMMAND:
			echo GOODBYE;
			exit(0);

		case CLEAR:
			for ($i = 0; $i < 100; $i++)
				echo EOL;
			break;

		case 'load':
			loadproject($args);
			break;

		default:
			$statement = strlen($args) ? $args[ strlen($args) - 1 ] === EOS : false;

			if ($statement) {
				global $textbuffer;
				$textbuffer = '';
				eval($args);
			}
			else {
				global $textbuffer, $runsbuffer;
				$runsbuffer = true;
				$textbuffer .= $args;
			}
			break;
	}
}
