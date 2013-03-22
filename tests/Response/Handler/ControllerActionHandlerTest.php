<?php

namespace Fabrico\Test\Response\Handler;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Request\HttpRequest;
use Fabrico\Response\HttpResponse;
use Fabrico\Response\Handler\ControllerActionHandler;

class ControllerActionHandlerTest extends Test
{
    public $handler;

    public $req;

    public $res;

    public $pub;

    public function setUp()
    {
        $this->handler = new ControllerActionHandler;
        $this->req = new HttpRequest;
        $this->res = new HttpResponse;
        $this->pub = new OvertClass(
            $this->req,
            'Fabrico\Response\Handler\ControllerActionHandler'
        );
    }

    public function testCanHandleRequestsWithActionAndController()
    {
        $data = [ '_action' => 'd' ];
        $this->req->setData($data);
        $this->assertTrue($this->handler->canHandle($this->req));
    }
}
