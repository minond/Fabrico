<?php

namespace Fabrico\Test\Mock\Event\Signal;

use Fabrico\Event\Signal;
use Fabrico\Event\Listener;

class UnusedSignal
{
    use Signal;

    public $func_called = false;

    public function func()
    {
        $this->signal(__FUNCTION__, Listener::PRE, func_get_args());
        $this->func_called = true;
        $this->signal(__FUNCTION__, Listener::POST, func_get_args());
    }
}
