<?php

namespace Fabrico\Renderer\StdHandlers;

use Fabrico\Application;
use Fabrico\Renderer\Handler;

/**
 * html file handler
 */
class HtmlHandler implements Handler
{
    /**
     * {@inheritDoc}
     */
    public function render(Application & $app, $file, array $data = [])
    {
        return file_get_contents($file);
    }
}

