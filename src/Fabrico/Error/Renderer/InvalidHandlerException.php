<?php

namespace Fabrico\Error\Renderer;

use Exception;

class InvalidHandlerException extends Exception
{
    /**
     * @param string $handler
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($handler, $code = 0, Exception $previous = null)
    {
        $message = 'Invalid render handler "%s". Expecting a callable, closure, or Fabrico\Renderer\Handler';
        $message = sprintf($message, $handler);
        parent::__construct($message, $code, $previous);
    }
}
