<?php

use Fabrico\Response\Http\Response;
use Fabrico\Request\Http\Request;
use Efficio\Http\Rule;

call_user_func(function() {
    require 'app.php';

    $res = new Response;
    $req = new Request;

    $req->setData($_REQUEST);
    $app->setRequest($req);
    $app->setResponse($res);

    // http handlers
    $req->addResponseHandlers($conf->get('project:handlers:http'));

    // $apirule = Rule::create(['/\/api\/(?P<model>[A-Za-z]+)(\/?)(?P<id>[A-Za-z0-9]+)?/'], [
    //     'controller' => 'Users',
    //     'action' => 'apicall',
    // ]);

    // $match = Rule::matching($_SERVER['PHP_SELF']);
    // $req->_controller = $match['controller'];
    // $req->_action = $match['action'];
    // var_dump($req);die;

    // pick best handler and send back response
    if ($req->prepareHandler($app) && $req->valid()) {
        $req->getHandler()->handle();
        $res->send();
    }
});
