<?php

namespace Fabrico\Renderer\StdHandlers;

use Fabrico\Renderer\Handler;

/**
 * html file handler
 */
class HtmlHandler implements Handler
{
    /**
     * {@inheritDoc}
     */
    public function render($file, array $data = [])
    {
        return file_get_contents($file);
    }
}

