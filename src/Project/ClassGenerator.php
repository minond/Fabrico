<?php

namespace Fabrico\Project;

use Fabrico\Core\Application;

/**
 * generates project class name
 * requires setting a static variable named $namespace
 */
trait ClassGenerator
{
    /**
     * generates a class' full namespace path
     * @param string $classname
     * @throws \Exception
     * @return string
     */
    private static function generateFullClassNamespacePath($classname)
    {
        if (!property_exists(get_called_class(), 'namespace') ||
            !static::$namespace
        ) {
            throw new \Exception(
                'ClassGenerator trait requires namespace information.');
        }

        return sprintf('\\%s\\%s\\%s',
            Application::getInstance()->getNamespace(),
            static::$namespace, $classname);
    }

    /**
     * checks if a project class exists
     * @param string $classname
     * @return boolean
     */
    private static function canFileProjectClass($classname)
    {
        return class_exists(self::generateFullClassNamespacePath($classname));
    }
}
