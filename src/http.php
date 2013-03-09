<?php

require 'Autoload.php';

call_user_func(function() {
	$app = new Fabrico\Core\Application;
	$req = new Fabrico\Request\HttpRequest;

	// parse project information
	$app->setRoot('/home/server/' . $_REQUEST['_project']);
	unset($_REQUEST['_project']);

	// parse view information
	$req->setViewFile($_REQUEST['_view']);
	unset($_REQUEST['_view']);

	// parse request information
	$req->setRequestData($_REQUEST);

	// save request and route to view
	if ($req->valid()) {
		die("Routing request");
		$app->setRequest($req);
	} else {
		die("Invalid request");
	}
});
