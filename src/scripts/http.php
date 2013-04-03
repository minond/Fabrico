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
    $req->setData($_REQUEST);
    $req->addResponseHandlers([
        'Fabrico\Response\Handler\ViewFileHandler',
        'Fabrico\Response\Handler\ControllerActionHandler',
    ]);

    $app->setNamespace('Propositum');
    $app->setRoot('/home/server/' . $_REQUEST['_project']);
    $app->setRequest($req);
    $app->setResponse($res);

    $conf = new Configuration;
    $listeners_info = $conf->loadProjectConfigurationFile(
        Configuration::LISTENERS);
    $listeners = new Listeners;
    $listeners->setListeners($listeners_info['Listeners']);
    $listeners->loadListeners();

    if (!$req->prepareHandler($app)) {
        die("Handler not found");
    } else if ($req->valid()) {
        $req->getHandler()->handle();
        $res->send();
    } else {
        die("Invalid request");
    }
});
