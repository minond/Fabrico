<?php

namespace Fabrico\Test\Request;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Request\DummyRequest;
use Fabrico\Test\Mock\Response\Handler\DummyHandler;
use Fabrico\Core\Application;
use Efficio\Http\Rule;

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

    public function testMultipleResponseHandlersCanBeAdded()
    {
        $this->req->addResponseHandlers(['one', 'two', 'three']);
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

    public function testHandlerSearchFailsWhenNoHandlersAreFound()
    {
        $app = new Application;
        $this->assertFalse($this->req->prepareHandler($app));
    }

    public function testBestHandlerIsActuallyFound()
    {
        $this->markTestIncomplete('Need to implement this in Request\Request');
    }

    public function testAddingEmptyRulesIsOk()
    {
        $this->assertNull($this->req->addRules([]));
    }

    public function testRulesAddTheirInformationToTheDataArray()
    {
        $data = [ '_uri' => '/infotest/123' ];
        $this->req->setData($data);
        $this->req->addRules([
            '/infotest/{id}' => []
        ]);
        $this->assertEquals('123', $this->req->id);
    }

    public function testControllerInformationIsPrefixedWithAnUnderscore()
    {
        $data = [ '_uri' => '/controllertest/red/1' ];
        $this->req->setData($data);
        $this->req->addRules([
            '/controllertest/{color}/{id}' => [
                'controller' => 'somecontroller',
            ]
        ]);
        $this->assertEquals('somecontroller', $this->req->_controller);
    }

    public function testActionInformationIsPrefixedWithAnUnderscore()
    {
        $data = [ '_uri' => '/actiontest/red/1' ];
        $this->req->setData($data);
        $this->req->addRules([
            '/actiontest/{color}/{id}' => [
                'action' => 'someaction',
            ]
        ]);
        $this->assertEquals('someaction', $this->req->_action);
    }
}
