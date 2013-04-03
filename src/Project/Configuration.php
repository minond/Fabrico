<?php

namespace Fabrico\Project;

use Fabrico\Project\FileFinder;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    use FileFinder;

    /**
     * configuration files:
     */
    const LISTENERS = 'listeners';
    const HANDLERS = 'handlers';
    const PROJECT = 'project';

    /**
     * configuration runtime cache
     * @var array
     */
    private $cache = [];

    /**
     * configuration directory (@app/config/)
     * @var string
     */
    protected static $dir = 'config';

    /**
     * configuration file extension (cofig.yml)
     * @var string
     */
    protected static $ext = '.yml';

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
     * @param string $config_file
     * @param string $prop*
     * @return mixed
     */
    public function get($config_file, $prop)
    {
        $config = $this->load($config_file);
        $props = func_get_args();
        array_shift($props);

        foreach ($props as $prop) {
            if (isset($config[ $prop ])) {
                $config = $config[ $prop ];
            } else {
                return null;
            }
        }

        return $config;
    }
}
