<?php

namespace Fabrico\Event;

/**
 * makes any class an observable object
 * <code>
 * <?php
 *
 * // example observable class
 * class User {
 *     use Observable;
 *
 *     public function greet($yourname) {
 *         $this->signal(__FUNCTION__, Listener::PRE, func_get_args());
 *         echo 'hi ' . $yourname;
 *         $this->signal(__FUNCTION__, Listener::POST, func_get_args());
 *     }
 * }
 * 
 * // example class listener
 * User::observe('greet', Listener::PRE, function($name) {
 *     // $this => instance
 *     // $name => $yourname
 * });
 * 
 * // example instance listener
 * $me = new User;
 * $me->subscribe('greet', Listener::PRE, function($name) {
 *     // $this => instance
 *     // $name => $yourname
 * });
 * 
 * ?>
 * </code>
 */
trait Observable
{
    /**
     * instance basedlisteners
     * @var Listener[]
     */
    protected $mylisteners = [];

    /**
     * class based listeners
     * @var Listener[]
     */
    protected static $listeners = [];

    /**
     * add a class listener
     * @param string $name
     * @param string $type
     * @param mixed callable|Closure $func
     * @return boolean
     */
    public static function observe($name, $type, $func)
    {
        static::$listeners[] = new Listener($name, $type, $func);
        return true;
    }

    /**
     * add a class listener
     * @param string $name
     * @param string $type
     * @param mixed callable|Closure $func
     * @return boolean
     */
    public function subscribe($name, $type, $func)
    {
        $this->mylisteners[] = new Listener($name, $type, $func);
        return true;
    }

    /**
     * trigger a signal
     * @param string $name
     * @param string $type
     * @param array $args
     */
    private function signal($name, $type, array $args = array())
    {
        foreach ([ static::$listeners, $this->mylisteners ] as $group) {
            foreach ($group as & $sub) {
                if ($sub->is($name, $type)) {
                    $sub->trigger($args);
                }

                unset($sub);
            }
        }
    }
}
