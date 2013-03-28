<?php

namespace Fabrico\Test\Mock\View;

use Fabrico\View\View;

class AnyView extends View
{
    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    public static function setExt($ext)
    {
        self::$ext = $ext;
    }
}
