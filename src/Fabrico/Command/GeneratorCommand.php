<?php

namespace Fabrico\Command;

/**
 * provides access to templates and other functions shared by commands that
 * are for generating files
 */
class GeneratorCommand extends Command
{
    /**
     * return a template file's content
     * @param string $file
     * @return string
     */
    protected function getTemplate($file)
    {
        return file_get_contents(sprintf('%s/templates/%s', __dir__, $file));
    }
}

