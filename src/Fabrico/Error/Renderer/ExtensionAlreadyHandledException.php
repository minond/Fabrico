<?php

namespace Fabrico\Error\Renderer;

use Exception;

class ExtensionAlreadyHandledException extends Exception
{
    /**
     * @param string $extension
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($extension, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Exception "%s" already handles!', $extension);
        parent::__construct($message, $code, $previous);
    }
}
