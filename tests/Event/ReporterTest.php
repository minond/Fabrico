<?php

namespace Fabrico\Test\Event;

use Fabrico\Event\Reporter;
use Fabrico\Test\Mock\Event\BasicObservable;
use Fabrico\Test\Mock\Event\UnusedObservable;
use Fabrico\Test\Mock\Event\QueuedObservable1;
use Fabrico\Test\Mock\Event\QueuedObservable2;
use Fabrico\Test\Test;

require_once 'tests/mocks/Event/Observable/BasicObservable.php';
require_once 'tests/mocks/Event/Observable/UnusedObservable.php';

class ReporterTest extends Test {
	public $reporter;
	public $basic = '\Fabrico\Test\Mock\Event\BasicObservable';
	public $queue1 = '\Fabrico\Test\Mock\Event\QueuedObservable1';
	public $queue2 = '\Fabrico\Test\Mock\Event\QueuedObservable2';
	public $unused = '\Fabrico\Test\Mock\Event\UnusedObservable';

	public function setUp() {
		$this->reporter = new Reporter;
	}

	public function testSubscriptionsArePlaced() {
		$called = false;
		$basic = new BasicObservable;

		$this->reporter->observe($this->basic, 'func', 'pre',
		function() use (& $called) {
			$called = true;
		});

		$basic->func();
		$this->assertTrue($called);
	}

	public function testSubscriptionsArePlacedOnCorrectObject() {
		$bcalled = false;
		$ucalled = false;
		$basic = new BasicObservable;
		$unused = new UnusedObservable;

		$this->reporter->observe($this->basic, 'func', 'pre',
		function() use (& $bcalled) {
			$bcalled = true;
		});

		$this->reporter->observe($this->unused, 'func', 'pre',
		function() use (& $ucalled) {
			$ucalled = true;
		});

		$basic->func();
		$unused->func();
		$this->assertTrue($bcalled);
		$this->assertTrue($ucalled);
	}

	public function testSubscriptionsToObjectsNotLoadedAreNotBound() {
		$this->reporter->observe($this->queue1, 'func', 'pre',
		function() use (& $called) {
			$called = true;
		});

		require 'tests/mocks/Event/Observable/QueuedObservable1.php';
		$called = false;
		$queue = new QueuedObservable1;

		$queue->func();
		$this->assertFalse($called);
	}

	public function testSubscriptionsToObjectsNotLoadedAreBoundAfterGreeting() {
		$this->reporter->observe($this->queue2, 'func', 'pre',
		function() use (& $called) {
			$called = true;
		});

		require 'tests/mocks/Event/Observable/QueuedObservable2.php';
		$called = false;
		$queue = new QueuedObservable2;

		$queue->func();
		$this->assertFalse($called);

		Reporter::greet($this->queue2);
		$queue->func();
		$this->assertTrue($called);
	}

	/**
	 * @expectedException Exception
	 */
	public function testGreetingTheReporterWithAnInvalidExeptionThrowsAnException() {
		Reporter::greet('Class_' . mt_rand());
	}
}
