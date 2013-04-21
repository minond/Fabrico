<?php

use Fabrico\Event\Listener;
use Fabrico\Event\Reporter;

Reporter::observe('Fabrico\View\View', 'render', Listener::PRE,
    function($info) {
        Twig_Autoloader::register();

        // todo: should check plugin configuration
        if ($info->extension === 'twig') {
            $load = new Twig_Loader_Filesystem($info->dirpath);
            $twig = new Twig_Environment($load);

            $info->content = $twig
                ->loadTemplate($info->filename)
                ->render($info->data);

            unset($twig);
        }
    }
);
