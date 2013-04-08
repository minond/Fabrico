<?php

namespace Fabrico\Test\Mock\Event;

use Fabrico\Event\Listeners;

class PublicListeners extends Listeners
{
    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    public static function setExt($ext)
    {
        self::$ext = $ext;
    }

    public function publicGetActiveListeners()
    {
        return $this->getActiveListeners();
    }
}
