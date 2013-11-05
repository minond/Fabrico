<?php

namespace Fabrico\Error\Renderer;

use Exception;

class NoViewsFoundException extends Exception
{
    /**
     * @param string $template
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($template, $code = 0, Exception $previous = null)
    {
        $message = sprintf('No views found. Template: "%s"', $template);
        parent::__construct($message, $code, $previous);
    }
}

