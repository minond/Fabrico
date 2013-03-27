<?php

namespace Fabrico\Test\Request;

use Fabrico\Test\Test;
use Fabrico\Request\HttpRequest;

class HttpRequestTest extends Test
{
    public function testHttpMethodCanBeSpecified()
    {
        $req = new HttpRequest(HttpRequest::GET);
        $this->assertEquals(HttpRequest::GET, $req->getMethod());
    }

    public function testControllerChecksServerSuperGlobalForMethodWhenNotSpecified()
    {
        $_SERVER['REQUEST_METHOD'] = HttpRequest::PUT;
        $req = new HttpRequest();
        $this->assertEquals(HttpRequest::PUT, $req->getMethod());
    }

    public function testControllerCanHandleAnEmptyServerSuperGlobal()
    {
        $_SERVER['REQUEST_METHOD'] = null;
        $req = new HttpRequest();
        $this->assertEquals('', $req->getMethod());
    }
}
