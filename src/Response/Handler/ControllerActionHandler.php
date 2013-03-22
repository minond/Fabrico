<?php

namespace Fabrico\Response\Handler;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Output\TextOutput;

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
    public function canHandle(Request & $req)
    {
        return !!$req->_action;
    }

    /**
     * checks if the controller has the requested method
     * @return boolean
     */
    public function valid()
    {
        return method_exists(
            $this->app->getController(),
            $this->app->getRequest()->_action
        );
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $res = $this->app->getResponse();
        $req = $this->app->getRequest();
        $out = new TextOutput;
        $res->setOutput($out);
        $ret = $this->app->getController()->{ $req->_action }($req, $res);

        if ($ret) {
            $out->setContent($ret);
        }
    }
}
