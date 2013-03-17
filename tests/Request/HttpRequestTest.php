<?php

namespace Fabrico\Test\Request;

use Fabrico\Request\HttpRequest;
use Fabrico\Test\Test;

class HttpRequestTest extends Test
{
    public $req;

    public function setUp()
    {
        $this->req = new HttpRequest;
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
}
