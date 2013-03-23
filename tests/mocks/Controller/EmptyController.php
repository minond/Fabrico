<?php

namespace Fabrico\Test\Mock\Controller;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Controller\Controller;
use Fabrico\Output\TextOutput;

class EmptyController extends Controller
{
    public static $expected_output = 'output';

    public static $function_called = false;

    public function sets_output(Request $req, Response $res)
    {
        self::$function_called = true;
        $out = new TextOutput;
        $out->setContent(self::$expected_output);
        $res->setOutput($out);
    }

    public function returns_output(Request $req, Response $res)
    {
        self::$function_called = true;
        return self::$expected_output;
    }
}
