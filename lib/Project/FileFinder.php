<?php

namespace Fabrico\Project;

use Fabrico\Core\Application;

/**
 * project file finder and loader
 * requires setting $dir and $ext static variables
 */
trait FileFinder
{
    protected static $filefindercache = [];

    /**
     * generates a file's path
     * @param string $filename
     * @param string $extension
     * @throws \Exception
     * @return string
     */
    public static function generateFileFilderFilePath($filename, $extension = '')
    {
        $hash = $filename . $extension;

        if (array_key_exists($hash, static::$filefindercache)) {
            return static::$filefindercache[ $hash ];
        }

        $caseinsensitive = property_exists(
            get_called_class(), 'caseinsensitive') &&
            static::$caseinsensitive;

        // incase we don't find it
        $dirpath = self::generateFileFilderDirectoryPath();
        $path = $dirpath . self::generateFileFilderFileName(
            $filename, $extension);

        // case insensitive search
        if ($caseinsensitive && !file_exists($path)) {
            $filename = strtolower($filename);
            $pattern = $dirpath . '*' . static::$ext;

            foreach (glob($pattern) as $fsfile) {
                $fname = self::getFileFinderFileName($fsfile);

                if ($filename === strtolower($fname)) {
                    $path = $dirpath .
                        self::generateFileFilderFileName($fname, $extension);
                    break;
                }
            }
        }

        static::$filefindercache[ $hash ] = $path;
        return $path;
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
            static::$dir . \DIRECTORY_SEPARATOR;
    }

    /**
     * appends the file format
     * @param string $filename
     * @param string $extension
     * @return string
     */
    public static function generateFileFilderFileName($filename, $extension = '')
    {
        // extension in filename?
        if (strpos($filename, '.') !== false) {
            $parts = explode('.', $filename);
            $extension = '.' . array_pop($parts);
            $filename = implode('.', $parts);
        }

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

    /**
     * cleans up a file and returns its name
     * @param string $file
     * @param string $extension - optional
     * @return string
     */
    public static function getFileFinderFileName($file, $extension = null)
    {
        $ext = $extension ?: static::$ext;
        $parts = explode(DIRECTORY_SEPARATOR, $file);
        $file = array_pop($parts);
        return preg_replace("/{$ext}$/", '', $file);
    }
}
