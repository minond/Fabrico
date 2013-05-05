<?php

use Fabrico\Event\Reporter;
use Fabrico\Core\Ext;

if (Ext::enabled('twig')) {
    Reporter::before('fabrico.view.view:render', function($info) {
        Twig_Autoloader::register();

        if ($info->extension === Ext::config('twig:extension')) {
            $load = new Twig_Loader_Filesystem($info->dirpath);
            $twig = new Twig_Environment($load);

            $info->content = $twig
                ->loadTemplate($info->filename)
                ->render($info->data);

            unset($twig);
        }
    });
}
