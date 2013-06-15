<?php

namespace %Namespace%\Controller;

use Fabrico\Request\Request;
use Fabrico\Response\Response;

class Hello
{
    /**
     * @public
     */
    public function index(Request $req, Response $res)
    {
        return 'Hello, World';
    }
}
