<?php

namespace Fabrico\Test\Event;

use Fabrico\Event\Listener;
use Fabrico\Test\Test;

class ListenerTest extends Test
{
    public function testListenersOfCorrentTypeAndLabelAreFound()
    {
        $listen = new Listener('rand', Listener::PRE, function() {});
        $this->assertTrue($listen->is('rand', Listener::PRE));
    }

    public function testListenersOfIncorrentTypeAndLabelAreIgnored()
    {
        $listen = new Listener('rand', Listener::PRE, function() {});
        $this->assertFalse($listen->is('rand', Listener::POST));
    }

    public function testArgumentsAreProperlyPassedToHandler()
    {
        $args = [1, 2, 3];
        $listen = new Listener('rand', Listener::PRE, function() {
            return func_get_args();
        });
        $this->assertEquals($args, $listen->trigger($args));
    }
}
