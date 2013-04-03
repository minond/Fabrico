<?php

namespace Fabrico\Event;

use Fabrico\Project\FileFinder;

/**
 * todo: add comment
 */
class Listeners
{
    use FileFinder;

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'listener';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $ext = '.php';

    /**
     * required listener properties in configuration file
     * @var array
     */
    private static $required_properties = ['name', 'active', 'tags'];

    /**
     * listeners to load
     * @var array
     */
    private $listeners = [];

    /**
     * returns a list of listeners that are flagged as active
     * @return array
     */
    private function getActiveListeners()
    {
        return array_filter($this->listeners, function($listener) {
            return (bool) $listener['active'];
        });
    }

    /**
     * checks all required information is set on all listener objects
     * @param array $listener
     * @throws \Exception
     * @return boolean
     */
    private function validateListeners(array $listeners)
    {
        foreach ($listeners as $index => $listener) {
            foreach (static::$required_properties as $prop) {
                if (!array_key_exists($prop, $listener)) {
                    throw new \Exception(sprintf(
                        'Listener "%s" missing "%s" property',
                        isset($listener['name']) ? $listener['name'] : $index,
                        $prop));
                }
            }
        }

        return true;
    }

    /**
     * listeners setter
     * @param array $listeners
     */
    public function setListeners(array $listeners)
    {
        if ($this->validateListeners($listeners)) {
            $this->listeners = $listeners;
        }
    }

    /**
     * listeners getter
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * load project listeners
     * @throws \Exception
     */
    public function loadListeners()
    {
        foreach ($this->getActiveListeners() as $listener) {
            $name = $listener['name'];

            if (!self::loadProjectFile($name)) {
                throw new \Exception(sprintf('Listener "%s" could not be found. Path: %s',
                    $name, self::generateFileFilderFilePath($name)));
            }
        }
    }
}
