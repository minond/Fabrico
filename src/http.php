<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Core\Job;

call_user_func(function() {
	$app = new Application;
	$job = new Job;
	$par = (object) $_REQUEST;

	// parse project information
	$app->setRoot('/home/server/' . $par->_project);
	unset($par->_project);

	// parse view information
	// $req->setViewFile($_REQUEST['_view']);
	// unset($_REQUEST['_view']);

	// parse request information
	// $req->setRequestData($_REQUEST);

	// save request and route to view
	// if ($req->valid()) {
		// die("Routing request");
	// } else {
		// die("Invalid request");
	// }

	echo("S");
});
