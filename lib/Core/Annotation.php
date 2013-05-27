<?php

namespace Fabrico\Core;

use ReflectionMethod;

/**
 * parses annotations
 */
abstract class Annotation
{
    /**
     * get an annotation's value. return values:
     *  - null: missing annotation
     *  - string: default
     *  - integer: annotation value = [0-9]
     *  - array: multiple annotation of same name set
     *  - boolean: annotation is set with no value,
     *             annotation value = 'true' | 'false'
     * @param mixed $class
     * @param string $method
     * @param string $annotation
     * @return mixed
     */
    public static function value($class, $method, $annotation)
    {
        $doc = self::parse($class, $method);
        return isset($doc[ $annotation ]) ? $doc[ $annotation ] : null;
    }

    /**
     * @param string $val
     * @return mixed
     */
    public static function convertStringValue($val)
    {
        if ($val === 'true') {
            return true;
        } else if ($val === 'false') {
            return false;
        } else if (is_numeric($val)) {
            if (strpos($val, '.') === false) {
                return (int) $val;
            } else {
                return (double) $val;
            }
        } else {
            return $val;
        }
    }

    /**
     * parse a doc comment
     *
     * TODO: should handle non-annotated and multi-line values
     *
     * @param mixed $class
     * @param string $method
     * @return array
     */
    public static function parse($class, $method)
    {
        $cname = is_string($class) ? $class : get_class($class);
        $reflection = new ReflectionMethod($class, $method);
        $comment = $reflection->getDocComment();
        $lines = explode(PHP_EOL, $comment);
        $parsed = [];

        array_pop($lines);
        array_shift($lines);

        foreach ($lines as & $line) {
            $line = preg_replace('/^\*\s+/', '', trim($line));

            if (strpos($line, '@') === 0) {
                $split = strpos($line, ' ');

                if ($split === false) {
                    $annotation = substr($line, 1);
                    $value = true;
                } else {
                    $annotation = substr($line, 1, $split - 1);
                    $value = self::convertStringValue(substr($line, $split + 1));
                }

                // add to parsed list
                if (isset($parsed[ $annotation ])) {
                    if (!is_array($parsed[ $annotation ])) {
                        $parsed[ $annotation ] = [ $parsed[ $annotation ] ];
                    }

                    $parsed[ $annotation ][] = $value;
                } else {
                    $parsed[ $annotation ] = $value;
                }
            }
        }

        return $parsed;
    }
}
