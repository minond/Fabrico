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
    private static $ext = '.php';

    /**
     * @see Fabrico\Project\ClassGenerator
     */
    private static $namespace = 'Controller';

    /**
     * @see Fabrico\Project\FileFilder
     */
    private static $dir = 'Controller';

    /**
     * load and instanciate a controller
     * @param string $name
     * @return Controller
     */
    public static function & load($name)
    {
        if (self::loadProjectFile($name) && self::canFileProjectClass($name)) {
            $class = self::generateFullClassNamespacePath($name);
            $class = new $class;
            return $class;
        }
    }
}
