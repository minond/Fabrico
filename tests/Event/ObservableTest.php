<?php

namespace Fabrico\Test;

use Fabrico\Event\Signal;
use Fabrico\Event\BasicObservable;

require 'tests/mocks/Event/Observable/BasicObservable.php';

class ObservableTest extends Test {
	public function testClassListenersCanBeSaved() {
		$this->assertTrue(BasicObservable::observe(
			'rand', Signal::PRE, function() {}));
	}

	public function testInstanceListenersCanBeSaved() {
		$obj = new BasicObservable;
		$this->assertTrue($obj->subscribe(
			'rand', Signal::PRE, function() {}));
	}

	public function testPreSignalsGoToAClassListener() {
		$called = false;
		$obj = new BasicObservable;

		BasicObservable::observe('func', Signal::PRE, function() use (& $called) {
			$called = true;
		});

		$obj->func();
		$this->assertTrue($called);
	}

	public function testPostSignalsGoToAClassListener() {
		$called = false;
		$obj = new BasicObservable;

		BasicObservable::observe('func', Signal::POST, function() use (& $called) {
			$called = true;
		});

		$obj->func();
		$this->assertTrue($called);
	}

	public function testPreSignalsGoToAnInstanceListener() {
		$called = false;
		$obj = new BasicObservable;

		$obj->subscribe('func', Signal::PRE, function() use (& $called) {
			$called = true;
		});

		$obj->func();
		$this->assertTrue($called);
	}

	public function testPostSignalsGoToAnInstanceListener() {
		$called = false;
		$obj = new BasicObservable;

		$obj->subscribe('func', Signal::POST, function() use (& $called) {
			$called = true;
		});

		$obj->func();
		$this->assertTrue($called);
	}

	public function testInstanceListenersAreNotTriggeredUnderDifferentObjects() {
		$called = false;
		$obj = new BasicObservable;
		$you = new BasicObservable;

		$obj->subscribe('func', Signal::POST, function() use (& $called) {
			$called = true;
		});

		$you->func();
		$this->assertFalse($called);
	}
}
