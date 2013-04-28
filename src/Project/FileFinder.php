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
     * @param string $extension
     * @throws \Exception
     * @return string
     */
    public static function generateFileFilderFilePath($filename, $extension = '')
    {
        return self::generateFileFilderDirectoryPath() .
            self::generateFileFilderFileName($filename, $extension);
    }

    /**
     * generates an object's directory path
     * @throws \Exception
     * @return string
     */
    public static function generateFileFilderDirectoryPath()
    {
        if (!property_exists(get_called_class(), 'dir') || !static::$dir) {
            throw new \Exception(
                'FileFinder trait requires directory information.');
        }

        return Application::getInstance()->getRoot() .
            \DIRECTORY_SEPARATOR . static::$dir . \DIRECTORY_SEPARATOR;
    }

    /**
     * appends the file format
     * @param string $filename
     * @param string $extension
     * @return string
     */
    public static function generateFileFilderFileName($filename, $extension = '')
    {
        return $extension ? $filename . $extension : $filename . static::$ext;
    }

    /**
     * checks to see if project file exists
     * @param string $filename
     * @param string $extension
     * @return boolean
     */
    public static function hasProjectFile($filename, $extension = '')
    {
        return file_exists(self::generateFileFilderFilePath($filename, $extension));
    }

    /**
     * loads a project's file
     * @param string $filename
     * @param string $extension
     * @throws \Exception
     * @return boolean
     */
    public static function loadProjectFile($filename, $extension = '')
    {
        if (self::hasProjectFile($filename)) {
            return require_once self::generateFileFilderFilePath($filename, $extension);
        } else {
            return false;
        }
    }
}
