<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\Configurable as Conf;

class Configurable
{
    use Conf;

    protected static $confpath;

    protected $var1;

    public function setConfpath($path)
    {
        static::$confpath = $path;
    }

    public function setVar1($val)
    {
        $this->var1 = $val;
    }

    public function triggerGetPropertyValue($key)
    {
        return $this->getPropertyValue($key);
    }
}
