<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\Configuration;

class NoWriteConfiguration extends Configuration
{
    public function set($path, $value)
    {
        $parts = self::parsePath($path);
        $config = $this->load($parts->base);
        $config =& $config;
        $finder =& $config;
        $last = count($parts->path) - 1;

        foreach ($parts->path as $i => $prop) {
            if (isset($finder[ $prop ])) {
                if ($i !== $last) {
                    $finder =& $finder[ $prop ];
                } else {
                    $finder[ $prop ] = $value;
                }
            } else {
                throw new \Exception("Invalid configuration path: {$path}");
            }
        }

        // update cache
        $this->cache[ $parts->base ] = $config;
        return true;
    }
}
