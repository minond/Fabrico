<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;

call_user_func(function() {
    $req = new HttpRequest;
    $req->setData($_REQUEST);
    $res = $req->respondWith();
    $out = $res->getOutput();

    $app = new Application;
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setRequest($req);
    $app->setResponse($res);

    // $req->addHandler

    if ($req->valid()) {
        $out->setContent('hi');
        $res->sendHeaders();
        $res->send();
    } else {
        die("Invalid request");
    }
});
