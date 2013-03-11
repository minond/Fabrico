<?php

namespace Fabrico\Test\Core;

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;
use Fabrico\Test\Test;

class ApplicationTest extends Test {
	public $app;

	public function setUp() {
		$this->app = new Application;
	}

	public function testRootCanBeSetAndRetrieved() {
		$this->app->setRoot('/');
		$this->assertEquals('/', $this->app->getRoot());
	}

	public function testRequestCanBeSetAndRetrieved() {
		$req = new HttpRequest;
		$this->app->setRequest($req);
		$this->assertEquals($req, $this->app->getRequest());
	}

	public function testResponseCanBeSetAndRetrieved() {
		$res = new HttpResponse;
		$this->app->setResponse($res);
		$this->assertEquals($res, $this->app->getResponse());
	}
}
