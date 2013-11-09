<?php

namespace Fabrico\Renderer\StdHandlers;

use Fabrico\Renderer\Handler;
use Twig_Environment as TwigEnv;
use Twig_Loader_Filesystem as TwigFs;

/**
 * twig file handler
 */
class TwigHandler implements Handler
{
    /**
     * {@inheritDoc}
     */
    public function render($file, array $data = [])
    {
        $fs = new TwigFs(getcwd());
        $twig = new TwigEnv($fs);

        if (file_exists('init/twig.php')) {
            call_user_func(function() use(& $twig, & $fs) {
                require_once 'init/twig.php';
            });
        }

        $template = $twig->loadTemplate($file);
        return $template->render($data);
    }
}

