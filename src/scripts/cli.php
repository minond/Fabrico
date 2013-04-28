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

    // project bootstraps
    if (count($conf->get('project:bootstrap'))) {
        foreach ($conf->get('project:bootstrap') as $file) {
            require_once FABRICO_PROJECT_ROOT . $file;
        }
    }


























});
