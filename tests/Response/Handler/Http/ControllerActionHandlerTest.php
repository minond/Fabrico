<?php

namespace Fabrico\Test\Response\Handler\Http;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Controller\EmptyController;
use Fabrico\Core\Application;
use Fabrico\Request\Http\Request;
use Fabrico\Response\Http\Response;
use Fabrico\Response\Handler\Http\ControllerActionHandler;
use Fabrico\Output\Http\HtmlOutput;

class ControllerActionHandlerTest extends Test
{
    public $handler;

    public $req;

    public $res;

    public $pub;

    public function setUp()
    {
        $this->handler = new ControllerActionHandler;
        $this->req = new Request;
        $this->res = new Response;
        $this->pub = new OvertClass(
            $this->req,
            'Fabrico\Response\Handler\Http\ControllerActionHandler'
        );
    }

    public function testRequestedActionIsParsedFromUrl()
    {
        $data = [ '_uri' => 'controller/view' ];
        $app = new Application;
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->req->setData($data);
        $this->handler->canHandle($this->req);
        $this->assertEquals('view', $this->handler->getAction());
    }

    public function testIndexActionIsParsedFromUrl()
    {
        $data = [ '_uri' => 'controller' ];
        $app = new Application;
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->req->setData($data);
        $this->handler->canHandle($this->req);
        $this->assertEquals('index', $this->handler->getAction());
    }

    public function testIndexActionIsParsedFromUrlWithSlash()
    {
        $data = [ '_uri' => 'controller/' ];
        $app = new Application;
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->req->setData($data);
        $this->handler->canHandle($this->req);
        $this->assertEquals('index', $this->handler->getAction());
    }

    public function testActionCanBeReadFromRequest()
    {
        $data = [ '_uri' => 'controller/', '_action' => 'myaction' ];
        $app = new Application;
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->req->setData($data);
        $this->handler->canHandle($this->req);
        $this->assertEquals('myaction', $this->handler->getAction());
    }

    public function testControllerCanBeReadFromRequest()
    {
        $data = [ '_uri' => 'controller/', '_controller' => 'EmptyController' ];
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);
        $app->setNamespace('Fabrico');
        $app->setRequest($this->req);
        $this->handler->setApplication($app);
        $this->req->setData($data);
        $this->handler->canHandle($this->req);
        $base = get_class(new EmptyController);
        $this->assertEquals($base, get_class($this->handler->getController()));
    }

    public function testControllerMethodIsCalled()
    {
        $data = [ '_uri' => 'Ignore/sets_output' ];
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
        $data = [ '_uri' => 'Ignore/sets_output' ];
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
        $data = [ '_uri' => 'Ignore/returns_output' ];
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

    public function testControllerMethodThatReturnNothingGetABlankHtmlOutputObject()
    {
        $data = [ '_uri' => 'Ignore/returns_nothing' ];
        $this->req->setData($data);
        $controller = new EmptyController;

        $app = new Application;
        $app->setRequest($this->req);
        $app->setResponse($this->res);

        $this->handler->setController($controller);
        $this->handler->setApplication($app);
        $this->handler->canHandle($this->req);
        $this->handler->handle();

        $this->assertTrue($this->res->getOutput() instanceof HtmlOutput);
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
