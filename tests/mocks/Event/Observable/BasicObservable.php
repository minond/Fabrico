<?php

namespace Fabrico\Test\Mock\Event\Observable;

use Fabrico\Event\Observable;
use Fabrico\Event\Listener;

class BasicObservable
{
    use Observable;

    public $func_called = false;

    public function func()
    {
        $this->signal(__FUNCTION__, Listener::PRE, func_get_args());
        $this->func_called = true;
        $this->signal(__FUNCTION__, Listener::POST, func_get_args());
    }
}
