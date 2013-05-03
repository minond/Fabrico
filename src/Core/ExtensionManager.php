<?php

namespace Fabrico\Core;

use Fabrico\Core\Application;
use Fabrico\Project\FileFinder;
use Fabrico\Project\Configuration;
use Fabrico\Output\Cli\Output;
use Fabrico\Event\Listeners;
use Symfony\Component\Yaml\Yaml;

/**
 * extension helper. installs and manages framework extensions
 */
class ExtensionManager
{
    use FileFinder;

    /**
     * path base
     * @var string
     */
    const CONFIGURATION_BASE = 'config';

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'ext';

    /**
     * @param Configuration $conf
     */
    public function __construct(Configuration $conf)
    {
        $this->configuration = $conf;
    }

    /**
     * extension configuration getter/setter.
     * @param string $path
     * @param mixed $value - optional
     * @throws \Exception
     * @return mixed
     */
    public function config($path, $value = null)
    {
        $parts = Configuration::parsePath($path);
        $realbase = self::$dir . DIRECTORY_SEPARATOR . $parts->base;
        $realpath = $realbase . Configuration::PATH_DELIM .
            implode(Configuration::PATH_DELIM, $parts->path);

        try {
            $this->configuration->load($realbase);
            return !isset($value) ? $this->configuration->get($realpath) :
                $this->configuration->set($realpath, $value);
        } catch (\Exception $error) {
            throw new \Exception("Invalid extension configuration path: {$path}");
        }
    }

    /**
     * returns true if project has enabled this extension
     * @param string $ext
     * @return boolean
     */
    public function enabled($ext)
    {
        return in_array(
            $ext,
            $this->configuration->get('ext:enabled')
        );
    }

    /**
     * enable an extension
     * @param string $ext
     * @return boolean
     */
    public function enable($ext)
    {
        $enabled = $this->configuration->get('ext:enabled');

        if (!in_array($ext, $enabled)) {
            $enabled[] = $ext;
        }

        return $this->configuration->set('ext:enabled', $enabled);
    }

    /**
     * disable an extension
     * @param string $ext
     * @return boolean
     */
    public function disable($ext)
    {
        return $this->configuration->set('ext:enabled', array_filter(
            $this->configuration->get('ext:enabled'), function($ex) use($ext) {
                return $ex !== $ext;
            })
        );
    }
}
