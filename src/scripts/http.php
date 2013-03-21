<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;

call_user_func(function() {
    $app = new Application;
    $res = new HttpResponse;
    $req = new HttpRequest;

    $req->setData($_REQUEST);
    $req->addResponseHandler('Fabrico\Response\Handler\ControllerActionHandler');

    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setRequest($req);
    $app->setResponse($res);

    $req->prepareHandler($app);
    $out = $res->getOutput();

    if ($req->valid()) {
        $req->getHandler()->handle($req, $res);
        $res->send();
    } else {
        die("Invalid request");
    }
});
