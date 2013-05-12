<?php

use Fabrico\Event\Reporter;
use Fabrico\Core\Ext;
use Twig_Loader_Filesystem as Loader;
use Twig_Environment as Twig;

if (Ext::enabled('twig')) {
    Reporter::before('fabrico.view.view:render', function($info) {
        if ($info->extension === Ext::config('twig:extension')) {
            $load = new Loader($info->dirpath);
            $twig = new Twig($load);

            $info->content = $twig
                ->loadTemplate($info->filename)
                ->render($info->data);
        }
    });
}
