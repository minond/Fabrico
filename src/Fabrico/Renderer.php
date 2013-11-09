<?php

namespace Fabrico;

use Closure;
use Fabrico\Application;
use Fabrico\Renderer\Handler;
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
     * @param Callable|Closure|Handler $handler
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
     * @param Application $app
     * @param string $file
     * @param arary $data, default: empty array
     * @throws InvalidExtentionException
     * @throws NoViewsFoundException
     * @throws MultipleViewsFoundException
     * @return string
     */
    public function render(Application $app, $file, array $data = [])
    {
        $template = $this->generateFileSearchString($file, array_keys($this->extension_map));
        $files = glob($template, GLOB_BRACE);
        $filec = count($files);
        $content = '';

        if ($filec === 1) {
            $file = array_pop($files);
            $ext = $this->parseFileExtension($file);

            if (isset($this->extension_map[ $ext ])) {
                $handler = $this->extension_map[ $ext ];

                if (is_string($handler) && class_exists($handler)) {
                    $handler = new $handler;
                    $this->extension_map[ $ext ] = $handler;
                }

                if ($handler instanceof Handler) {
                    $content = $handler->render($app, $file, $data);
                } else {
                    $content = call_user_func($handler, $app, $file, $data);
                }
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
}

