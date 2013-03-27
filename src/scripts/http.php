<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;
use Fabrico\Controller\Controller;

call_user_func(function() {
    $app = new Application;
    $req = new HttpRequest;
    $res = new HttpResponse;

    $req->setData($_REQUEST);
    $req->addResponseHandler('Fabrico\Response\Handler\ViewFileHandler');
    $req->addResponseHandler('Fabrico\Response\Handler\ControllerActionHandler');

    $app->setNamespace('Propositum');
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setRequest($req);
    $app->setResponse($res);

    if (!$req->prepareHandler($app)) {
        die("Handler not found");
    } else if ($req->valid()) {
        $req->getHandler()->handle();
        $res->send();
    } else {
        die("Invalid request");
    }
});
