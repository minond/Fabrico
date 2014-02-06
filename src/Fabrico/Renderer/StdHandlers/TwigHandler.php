<?php

namespace Fabrico\Renderer\StdHandlers;

use Fabrico\Application;
use Fabrico\Renderer\Handler;
use Twig_Environment as Twig;
use Twig_Loader_Filesystem as Loader;

/**
 * twig file handler
 */
class TwigHandler implements Handler
{
    /**
     * {@inheritDoc}
     */
    public function render(Application & $app, $file, array $data = [])
    {
        $cdir = getcwd();
        $twig = new Twig;
        $twig->setLoader(new Loader($cdir));
        $app->initialize('twig', [ 'twig' => & $twig ]);

        $template = $twig->loadTemplate($file);
        return $template->render($data);
    }
}
