<?php

namespace Fabrico\Test\Response;

use Fabrico\Response\HttpResponse;
use Fabrico\Output\TextOutput;
use Fabrico\Test\Test;

class HttpResponseTest extends Test {
	public $res;

	public function setUp() {
		$this->res = new HttpResponse;
	}

	public function testOutoutCanBeSetAndRetrieved() {
		$text = new TextOutput;
		$text->setContent('hi');
		$this->res->setOutput($text);
		$this->assertEquals($text, $this->res->getOutput());
	}

	public function testHeadersCanBeAdded() {
		$this->res->setHeader('hi', 'bye');
		$this->assertTrue($this->res->hasHeader('hi'));
	}

	public function testHeadersCanBeRetrieved() {
		$this->res->setHeader('hi', 'bye');
		$this->assertEquals('bye', $this->res->getHeader('hi'));
	}

	public function testHeadersCannotBeOverwritenByDefault() {
		$this->res->setHeader('hi', 'bye');
		$this->res->setHeader('hi', 'bye2');
		$this->assertEquals('bye', $this->res->getHeader('hi'));
	}

	public function testHeadersCanBeOverwritenWhenToldTo() {
		$this->res->setHeader('hi', 'bye');
		$this->res->setHeader('hi', 'bye2', true);
		$this->assertEquals('bye2', $this->res->getHeader('hi'));
	}

	public function testHeadersCanBeRemoved() {
		$this->res->setHeader('hi', 'bye');
		$this->res->removeHeader('hi');
		$this->assertFalse($this->res->hasHeader('hi'));
	}

	public function testMultipleHeadersCanBeSetAtOnce() {
		$this->res->setHeaders(['hi' => 'bye', 'two' => 'yes']);
		$this->assertTrue($this->res->hasHeader('hi'));
		$this->assertTrue($this->res->hasHeader('two'));
	}

	public function testResponseLoadsDefaultHeadersSetByTheOutputObject() {
		$text = new TextOutput;
		$headers = $text->getHeaders();
		$this->res->setOutput($text);

		foreach ($headers as $header => $value) {
			$this->assertTrue($this->res->hasHeader($header));
			$this->assertEquals($value, $this->res->getHeader($header));
		}
	}

	public function testOutputHeadersDoNotOverwriteHeadersAlreadySetInResponse() {
		$text = new TextOutput;
		$defval = 'thisisarandomstring';
		$headers = $text->getHeaders();

		foreach ($headers as $header => $value) break;

		// set the header to any value
		$this->res->setHeader($header, $defval);

		// then set the output object
		$this->res->setOutput($text);
		$this->assertEquals($defval, $this->res->getHeader($header));
	}

	public function testResponseNeedsAnOutput() {
		$this->assertFalse($this->res->ready());
	}

	public function testResponseCanBeSentAfterItHasAnOutputObject() {
		$text = new TextOutput;
		$this->res->setOutput($text);
		$this->assertTrue($this->res->ready());
	}

	public function testResponseOutputsTheOutputObjectsContents() {
		$this->expectOutputString('hi');
		$text = new TextOutput;
		$text->setContent('hi');
		$this->res->setOutput($text);
		$this->res->send();
	}
}
