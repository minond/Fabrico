<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\Http\Response;
use Fabrico\Request\Http\Request;
use Fabrico\Project\Configuration;
use Fabrico\Event\Listeners;
use Fabrico\Cache\RuntimeCache;

call_user_func(function() {
    $app = new Application;
    $res = new Response;
    $req = new Request;
    $conf = new Configuration(new RuntimeCache);

    $req->setData($_REQUEST);
    $app->setRequest($req);
    $app->setResponse($res);
    $app->setConfiguration($conf);
    $app->setRoot(FABRICO_PROJECT_ROOT);
    $app->setNamespace($conf->get('project:namespace'));

    // handlers
    $req->addResponseHandlers($conf->get('project:handlers:http'));

    // listeners
    if (count($conf->get('listeners'))) {
        $listeners = new Listeners;
        $listeners->setListeners($conf->get('listeners'));
        $listeners->loadListeners();
    }

    // pick best handler and send back response
    if ($req->prepareHandler($app) && $req->valid()) {
        $req->getHandler()->handle();
        $res->send();
    }
});
