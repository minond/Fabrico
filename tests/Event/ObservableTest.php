<?php

namespace Fabrico\Test\Event;

use Fabrico\Test\Mock\Event\BasicObservable;
use Fabrico\Test\Test;
use Fabrico\Event\Listener;

require 'tests/mocks/Event/Observable/BasicObservable.php';

class ObservableTest extends Test
{
    public function testClassListenersCanBeSaved()
    {
        $this->assertTrue(BasicObservable::observe(
            'rand', Listener::PRE, function() {}));
    }

    public function testInstanceListenersCanBeSaved()
    {
        $obj = new BasicObservable;
        $this->assertTrue($obj->subscribe(
            'rand', Listener::PRE, function() {}));
    }

    public function testPreSignalsGoToAClassListener()
    {
        $called = false;
        $obj = new BasicObservable;

        BasicObservable::observe('func', Listener::PRE, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testPostSignalsGoToAClassListener()
    {
        $called = false;
        $obj = new BasicObservable;

        BasicObservable::observe('func', Listener::POST, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testPreSignalsGoToAnInstanceListener()
    {
        $called = false;
        $obj = new BasicObservable;

        $obj->subscribe('func', Listener::PRE, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testPostSignalsGoToAnInstanceListener()
    {
        $called = false;
        $obj = new BasicObservable;

        $obj->subscribe('func', Listener::POST, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testInstanceListenersAreNotTriggeredUnderDifferentObjects()
    {
        $called = false;
        $obj = new BasicObservable;
        $you = new BasicObservable;

        $obj->subscribe('func', Listener::POST, function() use (& $called) {
            $called = true;
        });

        $you->func();
        $this->assertFalse($called);
    }
}
