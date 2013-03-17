<?php

namespace Fabrico\Response\Handler;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Controller\Controller;
use Fabrico\Controller\Manager as ControllerManager;

/**
 * routes a request though a controller's method matching the request
 */
class ControllerActionHandler extends Handler
{
    /**
     * @inheritdoc
     */
    protected static $level = self::HIGH;

    /**
     * @inheritdoc
     */
    public function canHandle(Request $req)
    {
        return !!$req->getController() && !!$req->getAction();
    }

    /**
     * @inheritdoc
     */
    public function handle(Request & $req, Response & $res)
    {
        $controller = $req->getController();
        $action = $req->getAction();

        $controller =& $this->getController($controller);
        $ret = $this->callActionMethod($controller, $action, $req, $res);
        $this->setResponseContent($res, $ret);
    }

    /**
     * @param Response $res
     * @param string $content
     */
    private function setResponseContent(Response $res, $content)
    {
        if ($content) {
            $res->getOutput()->setContent($content);
        }
    }

    /**
     * @param string $controller
     * @return Controller
     */
    private function getController($controller)
    {
        $manager = new ControllerManager($this->app);
        $controller =& $manager->get($controller);
        return & $controller;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @param Controller $controller
     * @param string $action
     * @return mixed string|void
     */
    private function callActionMethod(
        Request & $req,
        Response & $res,
        Controller & $controller,
        $action
    ) {
        return $controller->{ $action }($req, $res);
    }
}
