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
    public static function enabled($ext)
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
    public static function enable($ext)
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

    /**
     * install an extension
     * @param string $ext
     * @return boolean
     */
    public function install($ext)
    {
        $out = new Output;
        $listeners = [];
        $conf = Application::getInstance()->getConfiguration();
        $pattern = FABRICO_ROOT . self::$dir . DIRECTORY_SEPARATOR .
            '*/configuration.yml';

        foreach (glob($pattern) as $conffile) {
            $config = Yaml::parse($conffile);

            // found it
            if ($config['name'] === $ext) {
                $out->coutln('Installing {{ bold }}{{ purple }}%s{{ end }} extension', $ext);
                $dir = dirname($conffile) . DIRECTORY_SEPARATOR;

                // move source files
                $out->coutln('{{ section }}Moving source files into project{{ end }}');
                foreach ($config['source'] as $fileinfo) {
                    $file = $fileinfo['name'];
                    $local = $dir . $file;
                    $baseclass = null;

                    switch ($fileinfo['type']) {
                        case 'view':
                            $baseclass = 'Fabrico\View\View';
                            break;

                        case 'listener':
                            $listeners[] = Listeners::getFileFinderFileName($file);
                            $baseclass = 'Fabrico\Event\Listeners';
                            break;
                    }

                    if (is_string($baseclass)) {
                        $newloc = $baseclass::generateFileFilderFilePath($file);

                        if (self::mklink($local, $newloc)) {
                            $out->cout('{{ space }}{{ ok }}');
                        } else {
                            $out->cout('{{ space }}{{ fail }}');
                        }

                        $out->coutln(' Moving %s -> %s ', $file, $newloc);
                    }
                }

                // enable listeners, if any
                if (count($listeners)) {
                    $out->coutln('{{ section }}Enabling listeners{{ end }}');

                    foreach ($listeners as $listener) {
                        $project_listeners = $conf->load('listeners');
                        $in_project = false;

                        // check if we're already in project
                        foreach ($project_listeners as & $plistener) {
                            if ($plistener['name'] === $listener) {
                                $plistener['active'] = true;
                                $in_project = true;
                                unset($plistener);
                                break;
                            }

                            unset($plistener);
                        }

                        // add it
                        if (!$in_project) {
                            $tags = $config['tags'];
                            $tags[] = 'plugin';

                            $project_listeners[] = [
                                'name' => $listener,
                                'tags' => $tags,
                                'active' => true,
                            ];
                        }

                        $listeners_file = Configuration::generateFileFilderFilePath('listeners');
                        $project_listeners = Yaml::dump($project_listeners, 100, 2);

                        if (file_put_contents($listeners_file, $project_listeners) !== false) {
                            $out->cout('{{ space }}{{ ok }}');
                        } else {
                            $out->cout('{{ space }}{{ fail }}');
                        }

                        $out->coutln(' Enabling %s', $listener);
                    }
                }

                // add configuration
                $out->coutln('{{ section }}Saving extension configuration{{ end }}');
                $project_config = $config['config'];
                $project_config = Yaml::dump($project_config, 100, 2);
                $project_file = Configuration::generateFileFilderFilePath("ext/{$ext}");

                if (file_put_contents($project_file, $project_config) !== false) {
                    $out->cout('{{ space }}{{ ok }}');
                } else {
                    $out->cout('{{ space }}{{ fail }}');
                }

                $out->coutln(' Created %s', $project_file);

                // enable extension
                $out->coutln('{{ section }}Enabling extension{{ end }}');
                $enabled = $conf->load('ext');
                $enabled['enabled'][] = $ext;
                $enabled['enabled'] = array_unique($enabled['enabled']);
                $extfile = Configuration::generateFileFilderFilePath('ext');

                if (file_put_contents($extfile, Yaml::dump($enabled)) !== false) {
                    $out->cout('{{ space }}{{ ok }}');
                } else {
                    $out->cout('{{ space }}{{ fail }}');
                }

                $out->coutln(' Adding %s', $extfile);
            }
        }

        $out->coutln('{{ eol }}Successfully installed {{ bold }}{{ purple }}%s{{ end }} extension!', $ext);
        return true;
    }

    /**
     * creates a new symbolic link
     * @param string $local
     * @param string $newloc
     * @return boolean
     */
    private static function mklink($local, $newloc)
    {
        if (file_exists($newloc)) {
            unlink($newloc);
        }

        return symlink($local, $newloc);
    }
}
