<?php

namespace Fabrico;

use Closure;
use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\RuleBook;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;

class Application
{
    use EnvironmentInjenction;

    /**
     * @param Application
     */
    private static $app;

    /**
     * sets up some application properties
     * @param Application $app
     */
    public static function wire(Application $app)
    {
        $app->req = $app->getRequest();
        $app->res = $app->getResponse();
        $app->conf = $app->getConfiguration();
    }

    /**
     * bind the application that should be used by Application::call
     * @param Application $app
     */
    public static function bind(Application $app)
    {
        self::$app = $app;
    }

    /**
     * bind a function the Application::$app and call it
     * @param Closure $action
     * @return mixed
     */
    public static function call(Closure $action)
    {
        $action = Closure::bind($action, self::$app, get_class(self::$app));
        return $action();
    }

    /**
     * @return RuleBook
     */
    protected function getRuleBook()
    {
        $conf = $this->getConfiguration();
        $rules = new RuleBook;
        $rules->load($conf->get('routes'));

        return $rules;
    }

    /**
     * @param string $controller_name
     * @param string $namespace_name
     * @return string
     */
    protected function getControllerName($controller_name, $namespace_name)
    {
        $conf = $this->getConfiguration();
        return sprintf(
            '%s\\Controller\\%s',
            $namespace_name,
            ucwords($controller_name)
        );
    }

    /**
     * @param string $controller_name
     * @param string $action_name
     * @return string
     */
    protected function getViewFile($controller_name, $action_name)
    {
        return sprintf(
            'views/%s/%s',
            $controller_name,
            $action_name
        );
    }

    /**
     * handle a request
     */
    public function handle()
    {
        $req = $this->getRequest();
        $res = $this->getResponse();
        $rules = $this->getRuleBook();

        if ($route = $rules->matching($req, true)) {
            $action_name = $route['action'];
            $controller_name = $route['controller'];
            $namespace_name = $route['namespace'];
            $controller = $this->getControllerName($controller_name, $namespace_name);
            $view_file = $this->getViewFile($controller_name, $action_name);

            if (class_exists($controller)) {
                $controller = new $controller;

                if (method_exists($controller, $action_name) && is_callable([ $controller, $action_name ])) {
                    $out = $controller->{ $action_name }($req, $res);
                    $res->setStatusCode(Status::OK);

                    if (!$res->getContent() && isset($controller->responds_to)) {
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

    /**
     * send the request to the client
     */
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

