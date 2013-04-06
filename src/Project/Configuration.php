<?php

namespace Fabrico\Project;

use Fabrico\Project\FileFinder;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    use FileFinder;

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'config';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $ext = '.yml';

    /**
     * configuration runtime cache
     * @var array
     */
    protected $cache = [];

    /**
     * load and parse a configuration file
     * @param string $config_file
     * @return array
     */
    public function load($config_file)
    {
        if (!array_key_exists($config_file, $this->cache)) {
            $config = self::hasProjectFile($config_file) ? Yaml::parse(
                self::generateFileFilderFilePath($config_file)) : null;

            $this->cache[ $config_file ] = $config;
        }

        return $this->cache[ $config_file ];
    }

    /**
     * loads a configuration file and returns a configuration property
     * @param string $path
     * @return mixed
     */
    public function get($path)
    {
        $parts = explode(':', $path);
        $config = $this->load(array_shift($parts));

        foreach ($parts as $prop) {
            if (isset($config[ $prop ])) {
                $config = $config[ $prop ];
            } else {
                return null;
            }
        }

        return $config;
    }
}
