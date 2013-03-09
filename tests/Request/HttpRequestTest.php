<?php

namespace Fabrico\Test;

use Fabrico\Request\HttpRequest;

class HttpRequestTest extends \PHPUnit_Framework_TestCase {
	public function testRequestViewFilesCanBeSetAndRetrieved() {
		$view = 'hi';
		$req = new HttpRequest;
		$req->setViewFile($view);
		$this->assertEquals($view, $req->getViewFile());
	}

	public function testRequestMadeWithoutAViewFileAreInvalid() {
		$req = new HttpRequest;
		$this->assertFalse($req->valid());
	}

	public function testRequestMadeWithAViewFileAreValid() {
		$req = new HttpRequest;
		$req->setViewFile('hi');
		$this->assertTrue($req->valid());
	}
}
