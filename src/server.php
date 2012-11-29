<?php

$uri = $_SERVER['SCRIPT_NAME'];
$ext = isset(pathinfo($uri)['extension']) ?
	pathinfo($uri)['extension'] : '?';
$resources = ['js', 'css', 'gif', 'jpg', 'jpeg', 'png', 'ico'];
$headers = [
	'js' => [ 'Content-type: application/javascript' ]
];

$_REQUEST['_file'] = substr($uri, 1);

if (!$_REQUEST['_file']) {
	$_REQUEST['_file'] = 'index';
}

if (in_array($ext, $resources)) {

	if (array_key_exists($ext, $headers))
		foreach ($headers[ $ext ] as $header)
			header($header);

	$file = json_decode(file_get_contents('../configuration/httpconf.json'))->project->fsroot . $uri;

	if (file_exists($file))
		readfile($file);
}
else {
	require 'http.php';
}
