<?php

namespace Fabrico\Test\Mock\Response\Handler;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Response\Handler\Handler;

class DummyHandler extends Handler
{
    protected static $level = self::HIGH;

    public function canHandle(Request & $req)
    {
        return true;
    }

    public function valid()
    {
        return true;
    }

    public function handle()
    {
        return true;
    }
}
