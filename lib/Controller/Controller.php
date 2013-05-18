<?php

namespace Fabrico\Controller;

use Fabrico\Core\Application;
use Fabrico\Core\Annotation;
use Fabrico\Project\FileFinder;
use Fabrico\Project\ClassGenerator;

/**
 * base controller class
 */
abstract class Controller
{
    use FileFinder, ClassGenerator;

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $ext = '.php';

    /**
     * @see Fabrico\Project\ClassGenerator
     */
    protected static $namespace = 'Controller';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'controller';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $caseinsensitive = true;

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

    /**
     * @param mixed $controller
     * @param string $method
     * @return string
     */
    public static function isCallable($controller, $method)
    {
        $callable = false;

        if (
            is_object($controller) &&
            method_exists($controller, $method) &&
            is_callable([$controller, $method])
        ) {
            // check @public annotation
            $callable = !is_null(Annotation::value($controller, $method, 'public'));
        }

        return $callable;
    }
}
