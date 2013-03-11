<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;

call_user_func(function() {
	$app = new Application;
	$req = new HttpRequest;
	$res = new HttpResponse;
	$par = $_REQUEST;

	$app->setRoot('/home/server/' . $par['_project']);

	// build the request object
	if (isset($par['_controller'])) {
		$req->setController($par['_controller']);

		if (isset($par['_method'])) {
			$req->setMethod($par['_method']);
		} else if (isset($par['_action'])) {
			$req->setAction($par['_action']);
		}
	} else if (isset($par['_view'])) {
		$req->setViewFile($par['_view']);
	}

	// clean up the parameters object
	unset($par['_project']);
	unset($par['_view']);
	unset($par['_controller']);
	unset($par['_action']);
	unset($par['_method']);
	$req->setData($par);

	if ($req->valid()) {
		print_r($req);
		die("Routing request");
	} else {
		die("Invalid request");
	}
});
