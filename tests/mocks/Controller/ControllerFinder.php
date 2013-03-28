<?php

namespace Fabrico\Test\Mock\Controller;

use Fabrico\Controller\Controller;

class ControllerFinder extends Controller
{
    public static function setExt($ext)
    {
        self::$ext = $ext;
    }

    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    public static function setNs($ns)
    {
        self::$namespace = $ns;
    }
}
