<?php

namespace Fabrico\Core;

use Fabrico\Core\Application;
use Fabrico\Project\FileFinder;
use Fabrico\Project\Configuration;

/**
 * extension helper. installs and manages framework extensions
 */
class Ext
{
    use FileFinder;

    /**
     * path base
     * @var string
     */
    const CONFIGURATION_BASE = 'config';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'ext';

    /**
     * extension configuration getter. acts as a helper for Configuration::get
     * @param string $path
     * @throws \Exception
     * @return mixed
     */
    public static function config($path)
    {
        $conf = Application::getInstance()->getConfiguration();
        $parts = Configuration::parsePath($path);
        // array_unshift($parts->path, self::CONFIGURATION_BASE);

        $realbase = self::$dir . DIRECTORY_SEPARATOR . $parts->base;
        $realpath = $realbase . Configuration::PATH_DELIM .
            implode(Configuration::PATH_DELIM, $parts->path);

        try {
            $conf->load($realbase);
            return $conf->get($realpath);
        } catch (\Exception $error) {
            throw new \Exception("Invalid extension configuration path: {$path}");
        }
    }

    /**
     * returns true if project has enabled this extension
     * @param string $ext
     * @return boolean
     */
    public static function enabled($ext)
    {
        $conf = Application::getInstance()->getConfiguration();
        $enabled = $conf->get('ext:enabled');

        return $enabled && is_array($enabled) ?
            in_array($ext, $conf->get('ext:enabled')) : false;
    }
}
