<?php

namespace Fabrico\Renderer;

/**
 * file renderer handler
 */
interface Handler
{
    /**
     * @param string $file
     * @param array $data
     * @return string
     */
    public function render($file, array $data = []);
}

