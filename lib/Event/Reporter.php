<?php

namespace Fabrico\Event;

/**
 * manages object subscriptions. should have no dependencies, as it's loaded
 * immediately, and on every request.
 */
class Reporter
{
    /**
     * every subscription made to an object that does not exists yet is saved
     * in a queue to be added as soon as the object greets the reporter
     */
    private static $queue = [];

    /**
     * removed the first namespace slash
     * @param string $class
     * @return string
     */
    public static function cleanClassName($class)
    {
        return strtolower(
            str_replace('\\', '.',
                preg_replace('/^\\\/', '', $class)));
    }

    /**
     * reverts updates from cleanClassName
     * @param string $class
     * @return string
     */
    private static function realClassName($class)
    {
        return str_replace('.', '\\', $class);
    }

    /**
     * when new classes are loaded they should greet the reporter which will
     * then check the queue for any of their subscriptions
     * @param string $class
     * @throws \Exception
     */
    public static function greet($class)
    {
        $clean_class = self::cleanClassName($class);

        if (!class_exists($class)) {
            throw new \Exception("Unknown class: {$class}");
        }

        foreach (self::$queue as $index => & $sub) {
            if ($sub->class === $clean_class) {
                $class::observe($sub->name, $sub->type, $sub->action);
                unset(self::$queue[ $index ]);
            }

            unset($sub);
        }
    }

    /**
     * set subscriptions if object has been loaded or queues it.
     * @param string $class
     * @param string $name
     * @param string $type
     * @param mixed callable|Closure $action
     */
    public static function observe($class, $name, $type, $action)
    {
        $name = strtolower($name);
        $class = self::realClassName($class);

        if (!class_exists($class, false)) {
            self::$queue[] = (object) [
                'class' => self::cleanClassName($class),
                'name' => $name,
                'type' => $type,
                'action' => $action ];
        } else {
            $class::observe($name, $type, $action);
        }
    }

    /**
     * helper for Reporter::observe(Listener::PRE)
     * @see Reporter::observe
     * @param string $event
     * @param callable|Closure $action
     */
    public static function before($event, $action)
    {
        list($class, $name) = explode(':', $event);
        self::observe($class, $name, Listener::PRE, $action);
    }

    /**
     * helper for Reporter::observe(Listener::POST)
     * @see Reporter::observe
     * @param string $event
     * @param callable|Closure $action
     */
    public static function after($event, $action)
    {
        list($class, $name) = explode(':', $event);
        self::observe($class, $name, Listener::POST, $action);
    }
}
