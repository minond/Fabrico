<?php

use Fabrico\Response\Http\Response;
use Fabrico\Request\Http\Request;

call_user_func(function() {
    require 'app.php';

    $res = new Response;
    $req = new Request;

    $req->setData($_REQUEST);
    $app->setRequest($req);
    $app->setResponse($res);

    // http handlers
    $req->addResponseHandlers($conf->get('project:handlers:http'));

    // pick best handler and send back response
    if ($req->prepareHandler($app) && $req->valid()) {
        $req->getHandler()->handle();
        $res->send();
    }
});
