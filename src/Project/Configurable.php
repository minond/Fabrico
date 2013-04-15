<?php

namespace Fabrico\Project;

use Fabrico\Core\Application;

/**
 * lets classes access the project's configuration
 */
trait Configurable
{
    protected function getPropertyValue($prop)
    {
        if (!property_exists(get_called_class(), 'confpath') ||
            !static::$confpath
        ) {
            throw new \Exception(
                'Configurable trait requires a configuration path');
        }

        $full = implode(':', [
            static::$confpath,
            get_called_class(),
            $prop
        ]);

        return isset($this->{ $prop }) ? $this->{ $prop } :
            Application::getInstance()->getConfiguration()->get($full);
    }
}
