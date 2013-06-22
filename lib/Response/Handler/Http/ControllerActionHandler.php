<?php

namespace Fabrico\Response\Handler\Http;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Output\Output;
use Fabrico\Output\Http\HtmlOutput;
use Fabrico\Controller\Controller;
use Fabrico\Response\Handler\Handler;

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
     * saved during validation
     * @var Controller
     */
    protected $controller;

    /**
     * controller method name
     * @var string
     */
    protected $action;

    /**
     * force controller to be used
     * @param Controller
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * parsed controller getter
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * force controller method to be used
     * @string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * parsed controller method getter
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * checks if the controller has the requested method
     * @return true
     */
    public function canHandle(Request & $req)
    {
        $ok = false;
        $action = '';
        $controller = '';

        if ($req->_uri || $req->_controller) {
            if ($req->_controller) {
                $controller = $req->_controller;
            } else if (strpos($req->_uri, '/') !== false) {
                list($controller, $action) = explode('/', $req->_uri);
            } else {
                $controller = $req->_uri;
            }

            if (!$action) {
                if ($req->_action) {
                    $action = $req->_action;
                } else {
                    $action = 'index';
                }
            }

            $controller = Controller::load($controller);

            // keep set data
            $this->controller = $this->controller ?: $controller;
            $this->action = $this->action ?: $action;
        }

        return $this->controller &&
            Controller::isCallable($this->controller, $this->action);
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $res = $this->app->getResponse();
        $req = $this->app->getRequest();
        $ret = $this->controller->{ $this->action }($req, $res);

        if (is_scalar($ret)) {
            $out = new HtmlOutput;
            $out->setContent($ret);
            $ret =& $out;
        }

        if (!$res->getOutput()) {
            if (!is_object($ret) || !($ret instanceof Output)) {
                $ret = new HtmlOutput;
            }

            $res->setOutput($ret);
        }
    }
}
