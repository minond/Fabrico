<?php

namespace Fabrico\Test\Event;

use Fabrico\Test\Mock\Event\Signal\BasicSignal;
use Fabrico\Test\Test;
use Fabrico\Event\Listener;

class SignalTest extends Test
{
    public function testClassListenersCanBeSaved()
    {
        $this->assertTrue(BasicSignal::observe(
            'rand', Listener::PRE, function() {}));
    }

    public function testInstanceListenersCanBeSaved()
    {
        $obj = new BasicSignal;
        $this->assertTrue($obj->subscribe(
            'rand', Listener::PRE, function() {}));
    }

    public function testPreSignalsGoToAClassListener()
    {
        $called = false;
        $obj = new BasicSignal;

        BasicSignal::observe('func', Listener::PRE, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testPostSignalsGoToAClassListener()
    {
        $called = false;
        $obj = new BasicSignal;

        BasicSignal::observe('func', Listener::POST, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testPreSignalsGoToAnInstanceListener()
    {
        $called = false;
        $obj = new BasicSignal;

        $obj->subscribe('func', Listener::PRE, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testPostSignalsGoToAnInstanceListener()
    {
        $called = false;
        $obj = new BasicSignal;

        $obj->subscribe('func', Listener::POST, function() use (& $called) {
            $called = true;
        });

        $obj->func();
        $this->assertTrue($called);
    }

    public function testInstanceListenersAreNotTriggeredUnderDifferentObjects()
    {
        $called = false;
        $obj = new BasicSignal;
        $you = new BasicSignal;

        $obj->subscribe('func', Listener::POST, function() use (& $called) {
            $called = true;
        });

        $you->func();
        $this->assertFalse($called);
    }
}
