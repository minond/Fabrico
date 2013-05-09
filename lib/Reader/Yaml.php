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
        if (strpos($file, '{') === 0 || strpos($file, '[') === 0) {
            return json_decode($file, true);
        }

        return Y::parse($file);
    }

    public static function dump($obj)
    {
        // return json_encode($obj, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
        return Y::dump($obj, 100, 2);
    }
}
