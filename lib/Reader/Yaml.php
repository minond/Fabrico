<?php

namespace Fabrico\Reader;

use Symfony\Component\Yaml\Yaml as Y;

/**
 * acts as a wrapper for any Yaml parser
 */
abstract class Yaml
{
    public static function parse($file)
    {
        return Y::parse($file);
    }

    public static function dump($obj)
    {
        return Y::dump($obj, 100, 2);
    }
}
