<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\Configuration;

class ConfigurationFinder extends Configuration
{
    public static function setExt($ext)
    {
        self::$ext = $ext;
    }

    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setCache(array $cache)
    {
        $this->cache = $cache;
    }
}
