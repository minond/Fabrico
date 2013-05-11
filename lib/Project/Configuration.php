<?php

namespace Fabrico\Project;

use Fabrico\Project\FileFinder;
use Fabrico\Cache\Cache;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    use FileFinder;

    /**
     * configuration path delimeter. ie: project:handlers
     * @var string
     */
    const PATH_DELIM = ':';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'config';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $ext = '.yaml';

    /**
     * configuration runtime cache
     * @var Cache
     */
    protected $cache;

    /**
     * configuration place holders and patterns
     * @var array
     */
    public $placeholders = [
        'constants' => '/%([A-Z0-9_]+)/',
    ];

    /**
     * @param string $str
     * @return object
     */
    public static function parse($str)
    {
        return Yaml::parse($str);
    }

    /**
     * @param object $obj
     * @return string
     */
    public static function dump($obj)
    {
        return Yaml::dump($obj, 100, 2);
    }

    /**
     * configuration path parser. ie: project:handlers:http
     * - base: project
     * - path: [ handlers, http ]
     * @param string $path
     * @return \StdClass
     */
    public static function parsePath($path)
    {
        $parts = explode(self::PATH_DELIM, $path);
        $parsed = new \StdClass;
        $parsed->base = array_shift($parts);
        $parsed->path = $parts;

        return $parsed;
    }

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * cache getter
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * cache setter
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * load and parse a configuration file
     * @param string $config_file
     * @param boolean $raw - optional, default = false
     * @return array
     */
    public function load($config_file, $raw = false)
    {
        if (!$this->cache->has($config_file)) {
            $config = null;

            if (self::hasProjectFile($config_file)) {
                $str = file_get_contents(
                    self::generateFileFilderFilePath($config_file));

                if (!$raw) {
                    $str = $this->prepareRawConfigurationString($str);
                }

                $config = self::parse($str);
            }

            $this->cache[ $config_file ] = $config;
        }

        return $this->cache->has($config_file) ?
            $this->cache[ $config_file ] : null;
    }

    /**
     * loads a configuration file and returns a configuration property
     * @param string $path
     * @throws \Exception
     * @return mixed
     */
    public function get($path)
    {
        $parts = self::parsePath($path);
        $config = $this->load($parts->base);

        foreach ($parts->path as $prop) {
            if (isset($config[ $prop ])) {
                $config = $config[ $prop ];
            } else {
                throw new \Exception("Invalid configuration path: {$path}");
            }
        }

        return $config;
    }

    /**
     * configuration update
     * @param string $path
     * @param mixed $value
     * @return boolean - update success
     */
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

        // update file
        return file_put_contents(
            self::generateFileFilderFilePath($parts->base),
            self::dump($config)
        ) !== false;
    }

    /**
     * @param string $str
     * @return string
     */
    public function prepareRawConfigurationString($str)
    {
        preg_match_all($this->placeholders['constants'], $str, $constants);

        if (count($constants) && count($constants[1])) {
            foreach (array_unique($constants[1]) as $i => $const) {
                if (defined($const)) {
                    $str = str_replace($constants[0][ $i ], constant($const), $str);
                }
            }
        }

        return $str;
    }
}
