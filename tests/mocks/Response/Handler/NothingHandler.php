<?php

namespace Fabrico\Test\Mock\Response\Handler;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Response\Handler\Handler;

class NothingHandler extends DummyHandler
{
    public function canHandle(Request & $req)
    {
        return false;
    }
}
