<?php

namespace Fabrico\Test\Request\Http;

use Fabrico\Test\Test;
use Fabrico\Request\Http\Request;

class RequestTest extends Test
{
    public function testHttpMethodCanBeSpecified()
    {
        $req = new Request(Request::GET);
        $this->assertEquals(Request::GET, $req->getMethod());
    }

    public function testControllerChecksServerSuperGlobalForMethodWhenNotSpecified()
    {
        $_SERVER['REQUEST_METHOD'] = Request::PUT;
        $req = new Request();
        $this->assertEquals(Request::PUT, $req->getMethod());
    }

    public function testControllerCanHandleAnEmptyServerSuperGlobal()
    {
        $_SERVER['REQUEST_METHOD'] = null;
        $req = new Request();
        $this->assertEquals('', $req->getMethod());
    }
}
