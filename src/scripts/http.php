<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;
use Fabrico\Controller\Controller;
use Fabrico\Project\Configuration;

use Fabrico\Request\Rule;
use Fabrico\Request\Router;
use Fabrico\Event\Listeners;

call_user_func(function() {
    $app = new Application;
    $res = new HttpResponse;
    $req = new HttpRequest;
    $conf = new Configuration;
    $listeners = new Listeners;

    $req->setData($_REQUEST);
    $app->setRequest($req);
    $app->setResponse($res);
    $app->setConfiguration($conf);
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setNamespace($conf->get(Configuration::PROJECT,
        'Project', 'namespace'));

    // handlers
    $req->addResponseHandlers(
        $conf->get(Configuration::HANDLERS, 'Handlers'));

    // listeners
    $listeners->setListeners(
        $conf->get(Configuration::LISTENERS, 'Listeners'));
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
