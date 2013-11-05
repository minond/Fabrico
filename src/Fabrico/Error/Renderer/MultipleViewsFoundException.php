<?php

namespace Fabrico\Error\Renderer;

use Exception;

class MultipleViewsFoundException extends Exception
{
    /**
     * @param array $files
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(array $files, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Multiple view found: %s', implode(', ', $files));
        parent::__construct($message, $code, $previous);
    }
}

