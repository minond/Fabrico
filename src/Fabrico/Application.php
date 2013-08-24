<?php

namespace Fabrico;

use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\RuleBook;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;

class Application
{
    use EnvironmentInjenction;

    protected function getRuleBook()
    {
        static $rules;

        if (!$rules) {
            $conf = $this->getConfiguration();
            $rules = new RuleBook;
            $rules->load($conf->get('routes'));
        }

        return $rules;
    }

    protected function getControllerName($controller_name)
    {
        $conf = $this->getConfiguration();
        return sprintf(
            '%s\\Controller\\%sController',
            $conf->get('app:namespace'),
            ucwords($controller_name)
        );
    }

    protected function getViewFile($controller_name, $action_name)
    {
        return sprintf(
            'views/%s/%s',
            $controller_name,
            $action_name
        );
    }

    public function handle()
    {
        $req = $this->getRequest();
        $res = $this->getResponse();
        $rules = $this->getRuleBook();

        if ($route = $rules->matching($req, true)) {
            $action_name = $route['action'];
            $controller_name = $route['controller'];
            $controller = $this->getControllerName($controller_name);
            $view_file = $this->getViewFile($controller_name, $action_name);

            if (class_exists($controller)) {
                $controller = new $controller;

                if (method_exists($controller, $action_name)) {
                    $out = $controller->{ $action_name }($req, $res);
                    $res->setStatusCode(Status::OK);

                    if (!$res->getContent()) {
                        if (/* $controller->internalRespondsTo($req) */ true) {
                            // $controller->internalBuildResponse($res, $out);
                            $res->setContent($out);
                            $res->setContentType($controller->responds_to[0]);
                        }
                    }
                }
            }
        }
    }

    public function send()
    {
        $req = $this->getRequest();
        $res = $this->getResponse();

        if ($res->getStatusCode() === Status::NOT_FOUND) {
            $res->setContentType(Response::TEXT);
            $res->setContent('404, not found...');
        }

        $res->sendHeaders();
        $res->sendContent();
    }
}

