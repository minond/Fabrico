<?php

namespace Fabrico\Test\Request;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Request\DummyRequest;

require 'tests/mocks/Request/Request/DummyRequest.php';

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
}
