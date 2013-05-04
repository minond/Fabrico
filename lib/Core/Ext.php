<?php

namespace Fabrico\Core;

use Fabrico\Core\Application;
use Fabrico\Project\FileFinder;
use Fabrico\Project\Configuration;
use Fabrico\Output\Cli\Output;
use Fabrico\Event\Listeners;
use Symfony\Component\Yaml\Yaml;

/**
 * extension helper. installs and manages framework extensions
 */
class Ext
{
    /**
     * @var ExtensionManager
     */
    private static $em;

    /**
     * creates a new ExtensionManager if needed
     */
    private static function manager()
    {
        return self::$em = self::$em ?: new ExtensionManager(
            Application::getInstance()->getConfiguration());
    }

    /**
     * unsets $em
     */
    public static function hardreset()
    {
        self::$em = null;
    }

    /**
     * extension configuration getter/setter.
     * @param string $path
     * @param mixed $value - optional
     * @throws \Exception
     * @return mixed
     */
    public static function config($path, $value = null)
    {
        return self::manager()->config($path, $value);
    }

    /**
     * returns true if project has enabled this extension
     * @param string $ext
     * @return boolean
     */
    public static function enabled($ext)
    {
        return self::manager()->enabled($ext);
    }
}
