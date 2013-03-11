<?php

namespace Fabrico\Test\Request;

use Fabrico\Request\HttpRequest;
use Fabrico\Test\Test;

class HttpRequestTest extends Test {
	public $req;

	public function setUp() {
		$this->req = new HttpRequest;
	}

	public function testDataCanBeSetAndRetrieved() {
		$data = ['hi' => true];
		$this->req->setData($data);
		$this->assertEquals($data, $this->req->getData());
	}

	public function testViewFileCanBeSetAndRetrieved() {
		$this->req->setViewFile('hi');
		$this->assertEquals('hi', $this->req->getViewFile());
	}

	public function testControllerCanBeSetAndRetrieved() {
		$this->req->setController('hi');
		$this->assertEquals('hi', $this->req->getController());
	}

	public function testMethodCanBeSetAndRetrieved() {
		$this->req->setMethod('hi');
		$this->assertEquals('hi', $this->req->getMethod());
	}

	public function testActionCanBeSetAndRetrieved() {
		$this->req->setAction('hi');
		$this->assertEquals('hi', $this->req->getAction());
	}

	public function testDataPropertiesCanBeAccessedDirectlyFromTheClass() {
		$data = ['hi' => true];
		$this->req->setData($data);
		$this->assertTrue($this->req->hi);
	}

	public function testDataPropertiesCanBeSetDirectlyFromTheClass() {
		$val = 'anotherone';
		$data = ['hi' => true];
		$this->req->setData($data);
		$this->req->hi = $val;
		$this->assertEquals($val, $this->req->hi);
	}

	public function testHttpRequestsAreInvalidByDefault() {
		$this->assertFalse($this->req->valid());
	}

	public function testHttpRequestsAreValidOnceTheyGetAViewFile() {
		$this->req->setViewFile('hi');
		$this->assertTrue($this->req->valid());
	}

	public function testHttpRequestsAreValidOnceTheyGetAControllerAction() {
		$this->req->setController('hi');
		$this->req->setAction('hi');
		$this->assertTrue($this->req->valid());
	}

	public function testHttpRequestsAreValidOnceTheyGetAControllerMethod() {
		$this->req->setController('hi');
		$this->req->setMethod('hi');
		$this->assertTrue($this->req->valid());
	}
}
