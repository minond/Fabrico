<?php

namespace Fabrico\Project;

use Fabrico\Core\Application;

/**
 * project file finder and loader
 * requires setting $dir and $ext static variables
 */
trait FileFinder
{
    /**
     * generates a file's path
     * @param string $filename
     * @throws \Exception
     * @return string
     */
    private static function generateFileFilderFilePath($filename)
    {
        if (!property_exists(get_called_class(), 'dir') ||
            !property_exists(get_called_class(), 'ext') ||
            !static::$dir || !static::$ext
        ) {
            throw new \Exception(
                'FileFilder trait requires directory and file extension information.');
        }

        return Application::getInstance()->getRoot() .
            \DIRECTORY_SEPARATOR . static::$dir .
            \DIRECTORY_SEPARATOR . $filename .  static::$ext;
    }

    /**
     * checks to see if project file exists
     * @param string $filename
     * @return boolean
     */
    private static function hasProjectFile($filename)
    {
        return file_exists(self::generateFileFilderFilePath($filename));
    }

    /**
     * loads a project's file
     * @param string $filename
     * @throws \Exception
     * @return boolean
     */
    private static function loadProjectFile($filename)
    {
        if (self::hasProjectFile($filename)) {
            return require_once self::generateFileFilderFilePath($filename);
        } else {
            return false;
        }
    }
}
