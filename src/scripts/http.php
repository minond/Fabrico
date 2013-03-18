<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;

call_user_func(function() {
    $req = new HttpRequest;
    $req->setData($_REQUEST);
    $req->addResponseHandler('Fabrico\Response\Handler\ControllerActionHandler');

    $app = new Application;
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setRequest($req);

    $res = $req->generateResponse($app);
    $out = $res->getOutput();
    $app->setResponse($res);

    if ($req->valid()) {
        $out->setContent('hi');
        $req->handle($res);
        $res->send();
    } else {
        die("Invalid request");
    }
});
