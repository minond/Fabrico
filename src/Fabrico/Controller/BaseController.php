<?php

namespace Fabrico\Controller;

use Efficio\Http\Request;
use Efficio\Http\Response;

abstract class BaseController
{
    /**
     * standard actions
     */
    const INDEX_ACTION = 'index';

    /**
     * @var Request
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;

    /**
     * @param Request $req
     */
    public function setRequest(Request $req)
    {
        $this->req = $req;
    }

    /**
     * @param Response $res
     */
    public function setResponse(Response $res)
    {
        $this->res = $res;
    }
}

