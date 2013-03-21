<?php

namespace Fabrico\Test\Request;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Request\DummyRequest;
use Fabrico\Test\Mock\Response\Handler\DummyHandler;
use Fabrico\Core\Application;

require 'tests/mocks/Request/Request/DummyRequest.php';
require 'tests/mocks/Response/Handler/DummyHandler.php';
require 'tests/mocks/Response/Handler/NothingHandler.php';

class RequestTest extends Test
{
    public $req;

    public $pub;

    public function setUp()
    {
        $this->req = new DummyRequest;
        $this->pub = new OvertClass(
            $this->req,
            'Fabrico\Request\Request'
        );
    }

    public function testDataCanBeSetAndRetrieved()
    {
        $data = ['hi' => true];
        $this->req->setData($data);
        $this->assertEquals($data, $this->req->getData());
    }

    public function testDataPropertiesCanBeAccessedDirectlyFromTheClass()
    {
        $data = ['hi' => true];
        $this->req->setData($data);
        $this->assertTrue($this->req->hi);
    }

    public function testDataPropertiesCanBeSetDirectlyFromTheClass()
    {
        $val = 'anotherone';
        $data = ['hi' => true];
        $this->req->setData($data);
        $this->req->hi = $val;
        $this->assertEquals($val, $this->req->hi);
    }

    public function testResponseHandlersCanBeAdded()
    {
        $this->req->addResponseHandler('one');
        $this->req->addResponseHandler('two');
        $this->req->addResponseHandler('three');
        $this->assertEquals(['one', 'two', 'three'], $this->pub->handlers);
    }

    public function testHandlerCanBeRetrieved()
    {
        $handler = new DummyHandler;
        $this->pub->handler = $handler;
        $this->assertTrue($this->req->valid());
        $this->assertEquals($handler, $this->req->getHandler());
    }

    public function testBasicHandlersOnlyNeedAHandlerToBeValid()
    {
        $handler = new DummyHandler;
        $this->pub->handler = $handler;
        $this->assertTrue($this->pub->hasHandler());
    }

    public function testValidHandlerIsFoundByRequest()
    {
        $app = new Application;
        $this->req->addResponseHandler('Fabrico\Test\Mock\Response\Handler\NothingHandler');
        $this->req->addResponseHandler('Fabrico\Test\Mock\Response\Handler\DummyHandler');
        $this->assertFalse($this->pub->hasHandler());
        $this->req->prepareHandler($app);
        $this->assertTrue($this->pub->hasHandler());
    }

    /**
     * @expectedException Exception
     */
    public function testHandlerSearchFailsWhenNoHandlersAreFound()
    {
        $app = new Application;
        $this->req->prepareHandler($app);
    }

    public function testBestHandlerIsActuallyFound()
    {
        $this->markTestIncomplete('Need to implement this in Request\Request');
    }
}
