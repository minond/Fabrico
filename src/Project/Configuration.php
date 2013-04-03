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
    public function loadProjectConfigurationFile($config_file)
    {
        return self::hasProjectFile($config_file) ? Yaml::parse(
            self::generateFileFilderFilePath($config_file)) : null;
    }
}
