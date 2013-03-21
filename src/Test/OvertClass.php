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
    private $obj;

    /**
     * reflection classes of object we're making accessible
     * @var ReflectionClass
     */
    private $ref;

    /**
     * used to treat class different
     * @var string
     */
    private $mask;

    /**
     * @param mixed $obj
     * @param mask $mask
     */
    public function __construct($obj, $mask = null)
    {
        $this->mask = $mask ?: get_class($obj);
        $this->ref = new \ReflectionClass($this->mask);
        $this->obj = $obj;
    }

    /**
     * any property getter
     * @param string $var
     * @throws \Exception
     */
    public function __get($var)
    {
        if ($this->ref->hasProperty($var)) {
            $prop = $this->ref->getProperty($var);
            $prop->setAccessible(true);
            return $prop->getValue($this->obj);
        }

        throw new \Exception(
            sprintf('%s class does not have a "%s" property', $this->mask, $var));
    }

    /**
     * any property setter
     * @param string $var
     * @param mixed $val
     * @throws \Exception
     */
    public function __set($var, $val)
    {
        if ($this->ref->hasProperty($var)) {
            $prop = $this->ref->getProperty($var);
            $prop->setAccessible(true);
            return $prop->setValue($this->obj, $val);
        }

        throw new \Exception(
            sprintf('%s class does not have a "%s" property', $this->mask, $var));
    }

    /**
     * any method call
     * @param string $func
     * @param array $args
     */
    public function __call($func, $args)
    {
        $func = $this->ref->getMethod($func);
        $func->setAccessible(true);
        return $func->invokeArgs($this->obj, $args);
    }

    /**
     * any static method call
     * @param string $func
     * @param array $args
     */
    public static function __callStatic($func, $args)
    {
        $func = $this->ref->getMethod($func);
        $func->setAccessible(true);
        return $func->invokeArgs($this->obj, $args);
    }
}
