<?php

namespace Fabrico\Controller;

use Fabrico\Core\Application;
use Fabrico\Project\FileFinder;
use Fabrico\Project\ClassGenerator;

/**
 * base controller class
 */
abstract class Controller
{
    use FileFinder, ClassGenerator;

    /**
     * @see Fabrico\Project\FileFilder
     */
    protected static $ext = '.php';

    /**
     * @see Fabrico\Project\ClassGenerator
     */
    protected static $namespace = 'Controller';

    /**
     * @see Fabrico\Project\FileFilder
     */
    protected static $dir = 'controller';

    /**
     * load and instanciate a controller
     * @param string $name
     * @return Controller
     */
    public static function load($name)
    {
        if (self::loadProjectFile($name) && self::hasProjectClass($name)) {
            $class = self::generateFullClassNamespacePath($name);
            return new $class;
        }
    }
}
