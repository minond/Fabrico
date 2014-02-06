<?php

namespace Fabrico\Renderer;

use Fabrico\Application;

/**
 * file renderer handler
 */
interface Handler
{
    /**
     * @param Application & $app
     * @param string $file
     * @param array $data
     * @return string
     */
    public function render(Application & $app, $file, array $data = []);
}
