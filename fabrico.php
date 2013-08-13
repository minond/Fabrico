<?php

namespace fabrico;

use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\Rule;
use Efficio\Http\RuleBook;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;

require 'vendor/autoload.php';

$res = new Response;
$req = new Request(true);
$req->setUri($_SERVER['REDIRECT_URI']);

$conf = new Configuration;
$conf->setCache(new RuntimeCache);
$conf->setFormat(Configuration::YAML);
$conf->setDirectory('configuration');

$rules = new RuleBook;
$rules->load($conf->get('routes'));

if ($route = $rules->matching($req, true)) {
    if (isset($route['controller'])) {
        $namespace = $conf->get('app:namespace');
        $ctmpl = '%s\\Controller\\%sController';
        $action = $route['action'];
        $modelname = ucwords($route['model']);
        $controllername = ucwords($route['controller']);

        $controller = sprintf($ctmpl, $namespace, $modelname);

        // model controller exists?
        if (!class_exists($controller)) {
            // default controller
            $controller = sprintf($ctmpl, $namespace, $controllername);
        }

        if (class_exists($controller)) {
            $controller = new $controller;

            if (method_exists($controller, $action)) {
                $out = call_user_func([ $controller, $action ], $req);
                $res->setContent($out);
                $res->setStatusCode(Status::OK);

                if (is_object($out) || is_array($out)) {
                    $res->setContentType(Response::JSON);
                }
            }
        }
    }
} else {
    $res->setStatusCode(Status::NOT_FOUND);
}


$res->sendHeaders();
$res->sendContent();

