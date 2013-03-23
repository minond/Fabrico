<?php

namespace Fabrico\Test\Response\Handler;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Controller\EmptyController;
use Fabrico\Core\Application;
use Fabrico\Request\HttpRequest;
use Fabrico\Response\HttpResponse;
use Fabrico\Response\Handler\ControllerActionHandler;

require 'tests/mocks/Controller/EmptyController.php';

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

    public function testHanlderRequiresAnAction()
    {
        $data = [ '_invalid' => 'd' ];
        $this->req->setData($data);
        $this->assertFalse($this->handler->canHandle($this->req));
    }

    public function testCanHandleRequestsWithAction()
    {
        $data = [ '_action' => 'd' ];
        $this->req->setData($data);
        $this->assertTrue($this->handler->canHandle($this->req));
    }

    public function testHandlerRequiresAControllerToBeSet()
    {
        $data = [ '_action' => 'd' ];
        $this->req->setData($data);
        $app = new Application;
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->assertFalse($this->handler->valid());
    }

    public function testHandlerRequiresAValidControllerMethod()
    {
        $data = [ '_action' => 'd' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setController($controller);
        $this->handler->setApplication($app);
        $this->assertFalse($this->handler->valid());
    }

    public function testHandlerKnowsAboutValidControllerMethods()
    {
        $data = [ '_action' => 'sets_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setController($controller);
        $this->handler->setApplication($app);
        $this->assertTrue($this->handler->valid());
    }

    public function testControllerMethodIsCalled()
    {
        $data = [ '_action' => 'sets_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);
        $app->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->handle();
        $this->assertTrue(EmptyController::$function_called);
    }

    public function testControllerMethodCanSetResponseOutput()
    {
        $data = [ '_action' => 'sets_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);
        $app->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->handle();
        $this->assertEquals(
            EmptyController::$expected_output,
            $this->res->getOutput()->getContent()
        );
    }

    public function testControllerMethodCanReturnResponseOutput()
    {
        $data = [ '_action' => 'returns_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);
        $app->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->handle();
        $this->assertEquals(
            EmptyController::$expected_output,
            $this->res->getOutput()->getContent()
        );
    }
}
