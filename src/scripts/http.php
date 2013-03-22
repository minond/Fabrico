<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;
use Fabrico\Controller\Controller;

call_user_func(function() {
    $app = new Application;
    $res = new HttpResponse;
    $req = new HttpRequest;

    $req->setData($_REQUEST);
    $req->addResponseHandler('Fabrico\Response\Handler\ControllerActionHandler');

    $app->setNamespace('Propositum');
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setRequest($req);
    $app->setResponse($res);

    $app->setController(Controller::load('Users'));
    $req->prepareHandler($app);
    $out = $res->getOutput();

    if ($req->valid() && $req->getHandler()->valid()) {
        $req->getHandler()->handle();
        $res->send();
    } else {
        die("Invalid request");
    }
});
