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
    $listeners = new Listeners;
    $conf = new Configuration(new RuntimeCache);

    $req->setData($_REQUEST);
    $app->setRequest($req);
    $app->setResponse($res);
    $app->setConfiguration($conf);
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setNamespace($conf->get('project:namespace'));

    // handlers
    $req->addResponseHandlers($conf->get('handlers:http'));

    // listeners
    $listeners->setListeners($conf->get('listeners'));
    $listeners->loadListeners();

    if (!$req->prepareHandler($app)) {
        die('Handler not found');
    } else if ($req->valid()) {
        $req->getHandler()->handle();
        $res->send();
    } else {
        die("Invalid request");
    }
});
