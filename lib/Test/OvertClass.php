<?php

namespace Fabrico\Test;

/**
 * makes all functions and properties in a class accessible
 */
class OvertClass
{
    /**
     * the actual object
     * @var mixed
     */
    private $__obj;

    /**
     * reflection classes of object we're making accessible
     * @var ReflectionClass
     */
    private $__ref;

    /**
     * used to treat class different
     * @var string
     */
    private $__mask;

    /**
     * static functions overwriten
     * @var array
     */
    private static $__static_function_overwrites = [];

    /**
     * @param mixed $__obj
     * @param string $__mask
     */
    public function __construct($__obj, $__mask = null)
    {
        $this->__mask = $__mask ?: get_class($__obj);
        $this->__ref = new \ReflectionClass($this->__mask);
        $this->__obj = $__obj;
    }

    /**
     * any property getter
     * @param string $var
     * @throws \Exception
     */
    public function __get($var)
    {
        if ($this->__ref->hasProperty($var)) {
            $prop = $this->__ref->getProperty($var);
            $prop->setAccessible(true);
            return $prop->getValue($this->__obj);
        }

        throw new \Exception(
            sprintf('%s class does not have a "%s" property', $this->__mask, $var));
    }

    /**
     * any property setter
     * @param string $var
     * @param mixed $val
     * @throws \Exception
     */
    public function __set($var, $val)
    {
        if ($this->__ref->hasProperty($var)) {
            $prop = $this->__ref->getProperty($var);
            $prop->setAccessible(true);
            return $prop->setValue($this->__obj, $val);
        }

        throw new \Exception(
            sprintf('%s class does not have a "%s" property', $this->__mask, $var));
    }

    /**
     * any method call
     * @param string $func
     * @param array $args
     */
    public function __call($func, $args)
    {
        $func = $this->__ref->getMethod($func);
        $func->setAccessible(true);
        return $func->invokeArgs($this->__obj, $args);
    }

    /**
     * any static method call
     * @param string $func
     * @param array $args
     */
    public static function __callStatic($func, $args)
    {
        $ret = null;

        if (array_key_exists($func, self::$__static_function_overwrites)) {
            $ret = self::$__static_function_overwrites[ $func ];
        } else {
            $that = $args[ count($args) - 1 ];
            $func = $that->__ref->getMethod($func);
            $func->setAccessible(true);
            $ret = $func->invokeArgs($that, $args);
        }

        return $ret;
    }

    /**
     * overwrite a static function
     * @param string $name
     * @param mixed $ret
     */
    public function __overwriteStaticFunction($name, $ret)
    {
        self::$__static_function_overwrites[ $name ] = $ret;
    }
}
