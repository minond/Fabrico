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

    /**
     * used to test Request::respondWith returns a response with the correct
     * output object
     */
    public function dataProviderFormatOutputMap()
    {
        return [
            [ HttpRequest::HTML, '\Fabrico\Output\HtmlOutput' ],
            [ HttpRequest::TEXT, '\Fabrico\Output\TextOutput' ],
            [ HttpRequest::JSON, '\Fabrico\Output\JsonOutput' ],
        ];
    }

    /**
     * used to test all formats work
     */
    public function dataProviderAllFormatTypes()
    {
        return [
            [ HttpRequest::HTML ],
            [ HttpRequest::JSON ],
            [ HttpRequest::TEXT ],
        ];
    }

    public function testDataCanBeSetAndRetrieved()
    {
        $data = ['hi' => true];
        $this->req->setData($data);
        $this->assertEquals($data, $this->req->getData());
    }

    public function testFileCanBeSetAndRetrieved()
    {
        $this->req->setFile('hi');
        $this->assertEquals('hi', $this->req->getFile());
    }

    public function testControllerCanBeSetAndRetrieved()
    {
        $this->req->setController('hi');
        $this->assertEquals('hi', $this->req->getController());
    }

    public function testFormatCanBeSetAndRetrieved()
    {
        $this->req->setController(HttpRequest::JSON);
        $this->assertEquals(HttpRequest::JSON, $this->req->getController());
    }

    public function testMethodCanBeSetAndRetrieved()
    {
        $this->req->setMethod('hi');
        $this->assertEquals('hi', $this->req->getMethod());
    }

    public function testActionCanBeSetAndRetrieved()
    {
        $this->req->setAction('hi');
        $this->assertEquals('hi', $this->req->getAction());
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

    public function testHttpRequestsAreInvalidByDefault()
    {
        $this->assertFalse($this->req->valid());
    }

    public function testHttpRequestsAreValidOnceTheyGetAFile()
    {
        $this->req->setFile('hi');
        $this->assertTrue($this->req->valid());
    }

    public function testHttpRequestsRemoveExtensionsFromFiles()
    {
        $this->req->setFile('hi.html');
        $this->assertEquals('hi', $this->req->getFile());
    }

    /**
     * @dataProvider dataProviderAllFormatTypes
     */
    public function testHttpRequestsUpdateTheFormatsAccordingToTheFileRequested($format)
    {
        $this->req->setFile("hi.{$format}");
        $this->assertEquals($format, $this->req->getFormat());
    }

    /**
     * @expectedException Exception
     */
    public function testHttpRequestsDoNotSetInvalidFormats()
    {
        $this->req->setFormat('invalid');
    }

    public function testHttpRequestsAreValidOnceTheyGetAControllerAction()
    {
        $this->req->setController('hi');
        $this->req->setAction('hi');
        $this->assertTrue($this->req->valid());
    }

    public function testHttpRequestsAreValidOnceTheyGetAControllerMethod()
    {
        $this->req->setController('hi');
        $this->req->setMethod('hi');
        $this->assertTrue($this->req->valid());
    }

    /**
     * @dataProvider dataProviderFormatOutputMap
     */
    public function testOutputTypeMatchRequestTypes($format, $class)
    {
        $this->req->setFormat($format);
        $out = $this->req->respondWith()->getOutput();
        $this->assertInstanceOf($class, $out);
    }
}
