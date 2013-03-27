<?php

namespace Fabrico\Test\Response\Handler;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Controller\EmptyController;
use Fabrico\Core\Application;
use Fabrico\Request\HttpRequest;
use Fabrico\Response\HttpResponse;
use Fabrico\Response\Handler\ControllerActionHandler;

require_once 'tests/mocks/Controller/EmptyController.php';

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
        $app = new Application;
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->req->setData($data);
        $this->assertFalse($this->handler->canHandle($this->req));
    }

    public function testControllerMethodIsCalled()
    {
        $data = [ '_view' => 'Ignore/sets_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);
        $this->handler->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->canHandle($this->req);
        $this->handler->handle();
        $this->assertTrue(EmptyController::$function_called);
    }

    public function testControllerMethodCanSetResponseOutput()
    {
        $data = [ '_view' => 'Ignore/sets_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);
        $this->handler->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->canHandle($this->req);
        $this->handler->handle();
        $this->assertEquals(
            EmptyController::$expected_output,
            $this->res->getOutput()->getContent()
        );
    }

    public function testControllerMethodCanReturnResponseOutput()
    {
        $data = [ '_view' => 'Ignore/returns_output' ];
        $this->req->setData($data);
        $controller = new EmptyController;
        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);
        $this->handler->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->canHandle($this->req);
        $this->handler->handle();
        $this->assertEquals(
            EmptyController::$expected_output,
            $this->res->getOutput()->getContent()
        );
    }

    public function testControllerGetterAndSetter()
    {
        $controller = new EmptyController;
        $this->handler->setController($controller);
        $this->assertEquals($controller, $this->handler->getController());
    }

    public function testActionGetterAndSetter()
    {
        $action = 'action_name';
        $this->handler->setAction($action);
        $this->assertEquals($action, $this->handler->getAction());
    }
}
