<?php

$uri = $_SERVER['SCRIPT_NAME'];
$ext = isset(pathinfo($uri)['extension']) ?
	pathinfo($uri)['extension'] : '?';

$resources = [
	'js', 'css', 'gif', 'jpg',
	'jpeg', 'png', 'ico'
];

$headers = [
	'js' => [ 'Content-type: application/javascript' ],
	'css' => [ 'Content-type: text/css' ],
];

$colors = [
	'0;34',
	'0;32',
	'1;32',
	'0;36',
	'1;36',
	'0;31',
	'1;31',
	'0;35',
	'1;35',
	'0;33',
];


$_REQUEST['_file'] = substr($uri, 1);

if (!$_REQUEST['_file'])
	$_REQUEST['_file'] = 'index';

if (in_array($ext, $resources)) {
	if (array_key_exists($ext, $headers))
		foreach ($headers[ $ext ] as $header)
			header($header);

	$file = json_decode(
		file_get_contents('../configuration/httpconf.json')
	)->project->fsroot . $uri;

	if (file_exists($file))
		readfile($file);
}
else {
	$color = $colors[ mt_rand(0, count($colors) - 1) ];
	$f = fopen('php://stderr', 'w');
	fputs($f, "\033[{$color}m\033[1m/{$_REQUEST['_file']}\033[0m\033[0m");
	$color = $colors[ mt_rand(0, count($colors) - 1) ];
	fputs($f, " \033[{$color}m");
	fputs($f, json_encode($_REQUEST, JSON_PRETTY_PRINT));
	fputs($f, "\033[0m");
	fputs($f, "\n\n");
	fclose($f);
	require 'http.php';
}
