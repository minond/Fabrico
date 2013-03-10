<?php

namespace Fabrico\Test\Mock\Event;

use Fabrico\Event\Observable;
use Fabrico\Event\Signal;

class QueuedObservable1 {
	use Observable;

	public $func_called = false;

	public function func() {
		$this->signal(__FUNCTION__, Signal::PRE, func_get_args());
		$this->func_called = true;
		$this->signal(__FUNCTION__, Signal::POST, func_get_args());
	}
}
