<?php

namespace Fabrico\Request;

/**
 * handles all http requests
 */
class HttpRequest extends Request
{
    /**
     * http methods
     */
    const DEL = 'DELETE';
    const GET = 'GET';
    const HEAD = 'HEAD';
    const POST = 'POST';
    const PUT = 'PUT';

    /**
     * http method
     * @var string
     */
    private $method;

    /**
     * @param string $method
     */
    public function __construct($method = null)
    {
        $this->method = $method ?: (isset($_SERVER['REQUEST_METHOD']) ?
            $_SERVER['REQUEST_METHOD'] : '');
    }

    /**
     * method getter
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
