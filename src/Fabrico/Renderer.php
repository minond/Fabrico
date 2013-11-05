<?php

namespace Fabrico;

use Closure;
use StdClass;
use Exception;
use Twig_Environment as TwigEnv;
use Twig_Loader_Filesystem as TwigFs;
use Fabrico\Error\Renderer\InvalidExtentionException;
use Fabrico\Error\Renderer\NoViewsFoundException;
use Fabrico\Error\Renderer\MultipleViewsFoundException;
use Fabrico\Error\Renderer\ExtensionAlreadyHandledException;

class Renderer
{
    /**
     * extension handlers
     * @var string[Callable]
     */
    protected $extension_map = [];

    /**
     * @param string $ext
     * @param Callable|Closure $handler
     * @throws ExtensionAlreadyHandledException
     */
    public function handler($ext, $handler, $overwrite = false)
    {
        if (!$overwrite && isset($this->extension_map[ $ext ])) {
            throw new ExtensionAlreadyHandledException($ext);
        }

        $this->extension_map[ $ext ] = $handler;
    }

    /**
     * render a view file
     * @param string $file
     * @param arary $data, default: empty array
     * @throws InvalidExtentionException
     * @throws NoViewsFoundException
     * @throws MultipleViewsFoundException
     * @return string
     */
    public function render($file, array $data = [])
    {
        $template = $this->generateFileSearchString($file, array_keys($this->extension_map));
        $files = glob($template, GLOB_BRACE);
        $filec = count($files);
        $content = '';

        if ($filec === 1) {
            $file = array_pop($files);
            $ext = $this->parseFileExtension($file);

            if (isset($this->extension_map[ $ext ])) {
                $content = call_user_func($this->extension_map[ $ext ],
                    $file, $data);
            } else {
                throw new InvalidExtentionException($ext);
            }
        } else if ($filec === 0) {
            throw new NoViewsFoundException($template);
        } else {
            throw new MultipleViewsFoundException($files);
        }

        return $content;
    }

    /**
     * generate a glob file search string
     * @param string $file
     * @param array $extensions, default; empty array
     */
    protected function generateFileSearchString($file, array $extensions = [])
    {
        return sprintf('%s{%s}', $file, implode('', array_map(function($ext) {
            return sprintf(',.%s', $ext);
        }, $extensions)));
    }

    /**
     * get the extension from a file name
     * @param string $file
     */
    protected function parseFileExtension($file)
    {
        return substr($file, strrpos($file, '.') + 1);
    }

    /**
     * php file handler
     * @param string $file
     * @param array $data
     */
    public function stdPhpHandler($file, array $data = [])
    {
        return call_user_func(Closure::bind(function() use ($file) {
            ob_start();
            require $file;
            return ob_get_clean();
        }, (object) $data ?: new StdClass));
    }

    /**
     * html file handler
     * @param string $file
     * @param array $data
     */
    public function stdHtmlHander($file, array $data = [])
    {
        return file_get_contents($file);
    }

    /**
     * twig file handler
     * @param string $file
     * @param array $data
     */
    public function stdTwigHander($file, array $data = [])
    {
        // $dir = dirname(dirname($file));
        $fs = new TwigFs(getcwd());
        $twig = new TwigEnv($fs);

        if (file_exists('init/twig.php')) {
            call_user_func(function() use(& $twig, & $fs) {
                require_once 'init/twig.php';
            });
        }

        // Application::init('twig', [
        //     'twig' => & $twig,
        //     'fs' => & $fs,
        // ]);

        $template = $twig->loadTemplate($file);
        return $template->render($data);
    }
}

