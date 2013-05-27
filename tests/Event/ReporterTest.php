<?php

namespace Fabrico\Test\Event;

use Fabrico\Event\Reporter;
use Fabrico\Test\Mock\Event\Observable\BasicObservable;
use Fabrico\Test\Mock\Event\Observable\UnusedObservable;
use Fabrico\Test\Mock\Event\Observable\QueuedObservable1;
use Fabrico\Test\Mock\Event\Observable\QueuedObservable2;
use Fabrico\Test\Test;

class ReporterTest extends Test
{
    public $basic  = '\Fabrico\Test\Mock\Event\Observable\BasicObservable';
    public $queue1 = '\Fabrico\Test\Mock\Event\Observable\QueuedObservable1';
    public $queue2 = '\Fabrico\Test\Mock\Event\Observable\QueuedObservable2';
    public $unused = '\Fabrico\Test\Mock\Event\Observable\UnusedObservable';

    public function testSubscriptionsArePlaced()
    {
        $called = false;
        $basic = new BasicObservable;

        Reporter::observe($this->basic, 'func', 'pre', function() use (& $called) {
            $called = true;
        });

        $basic->func();
        $this->assertTrue($called);
    }

    public function testSubscriptionsArePlacedUsingHelperBeforeFunction()
    {
        $called = false;
        $basic = new BasicObservable;

        Reporter::before('fabrico.test.mock.event.observable.basicobservable:func', function() use (& $called) {
            $called = true;
        });

        $basic->func();
        $this->assertTrue($called);
    }

    public function testSubscriptionsArePlacedUsingHelperAfterFunction()
    {
        $called = false;
        $basic = new BasicObservable;

        Reporter::after('fabrico.test.mock.event.observable.basicobservable:func', function() use (& $called) {
            $called = true;
        });

        $basic->func();
        $this->assertTrue($called);
    }

    public function testSubscriptionsArePlacedOnCorrectObject()
    {
        $bcalled = false;
        $ucalled = false;
        $basic = new BasicObservable;
        $unused = new UnusedObservable;

        Reporter::observe($this->basic, 'func', 'pre', function() use (& $bcalled) {
            $bcalled = true;
        });

        Reporter::observe($this->unused, 'func', 'pre', function() use (& $ucalled) {
            $ucalled = true;
        });

        $basic->func();
        $unused->func();
        $this->assertTrue($bcalled);
        $this->assertTrue($ucalled);
    }

    public function testSubscriptionsToObjectsNotLoadedAreNotBoundWhenNotGreetedByTheReporter()
    {
        Reporter::observe($this->queue1, 'func', 'pre', function() use (& $called) {
            $called = true;
        });

        $called = false;
        $queue = new QueuedObservable1;

        $queue->func();
        $this->assertFalse($called);
    }

    public function testSubscriptionsToObjectsNotLoadedAreBoundAfterGreeting()
    {
        Reporter::observe($this->queue2, 'func', 'pre', function() use (& $called) {
            $called = true;
        });

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
    public function testGreetingTheReporterWithAnInvalidExeptionThrowsAnException()
    {
        Reporter::greet('Class_' . mt_rand());
    }
}
