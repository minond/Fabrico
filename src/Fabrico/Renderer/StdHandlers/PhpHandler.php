<?php

namespace Fabrico\Renderer\StdHandlers;

use StdClass;
use Closure;
use Fabrico\Renderer\Handler;

/**
 * php file handler
 */
class PhpHandler implements Handler
{
    /**
     * {@inheritDoc}
     */
    public function render($file, array $data = [])
    {
        return call_user_func(Closure::bind(function() use ($file) {
            ob_start();
            require $file;
            return ob_get_clean();
        }, (object) $data ?: new StdClass));
    }
}

