<?php

namespace Fabrico\Error\Renderer;

use Exception;

class InvalidExtentionException extends Exception
{
    /**
     * @param string $ext
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($ext, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Invalid extension "%s"', $ext);
        parent::__construct($message, $code, $previous);
    }
}
