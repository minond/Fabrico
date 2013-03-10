<?php

namespace Fabrico\Test;

use Fabrico\Event\Listener;
use Fabrico\Event\Signal;

class ListenerTest extends Test {
	public function testListenersOfCorrentTypeAndLabelAreFound() {
		$listen = new Listener('rand', Signal::PRE, function() {});
		$this->assertTrue($listen->is('rand', Signal::PRE));
	}

	public function testListenersOfIncorrentTypeAndLabelAreIgnored() {
		$listen = new Listener('rand', Signal::PRE, function() {});
		$this->assertFalse($listen->is('rand', Signal::POST));
	}

	public function testArgumentsAreProperlyPassedToHandler() {
		$args = [1, 2, 3];
		$listen = new Listener('rand', Signal::PRE, function() {
			return func_get_args();
		});
		$this->assertEquals($args, $listen->trigger($args));
	}
}
