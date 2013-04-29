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
class Ext
{
    use FileFinder;

    /**
     * path base
     * @var string
     */
    const CONFIGURATION_BASE = 'config';

    /**
     * @see Fabrico\Project\FileFinder
     */
    protected static $dir = 'ext';

    /**
     * extension configuration getter. acts as a helper for Configuration::get
     * @param string $path
     * @throws \Exception
     * @return mixed
     */
    public static function config($path)
    {
        $conf = Application::getInstance()->getConfiguration();
        $parts = Configuration::parsePath($path);
        $realbase = self::$dir . DIRECTORY_SEPARATOR . $parts->base;
        $realpath = $realbase . Configuration::PATH_DELIM .
            implode(Configuration::PATH_DELIM, $parts->path);

        try {
            $conf->load($realbase);
            return $conf->get($realpath);
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
        $conf = Application::getInstance()->getConfiguration();
        $enabled = $conf->get('ext:enabled');

        return $enabled && is_array($enabled) ?
            in_array($ext, $conf->get('ext:enabled')) : false;
    }

    /**
     * enable an extension
     * @param string $ext
     * @return boolean
     */
    public static function enable($ext)
    {
        $out = new Output;
        $conf = Application::getInstance()->getConfiguration();
        $project_ext = $conf->load('ext');
        $project_ext['enabled'][] = $ext;
        $project_ext['enabled'] = array_unique($project_ext['enabled']);
        natcasesort($project_ext['enabled']);

        $ok = file_put_contents(
            Configuration::generateFileFilderFilePath('ext'),
            Yaml::dump($project_ext)
        );

        if ($ok) {
            $out->coutln('Successfully enabled {{ bold }}{{ purple }}%s{{ end }}', $ext);
        } else {
            $out->coutln('There war an error enabling {{ bold }}{{ purple }}%s{{ end }}', $ext);
        }

        return $ok;
    }

    /**
     * disable an extension
     * @param string $ext
     * @return boolean
     */
    public static function disable($ext)
    {
        $out = new Output;
        $conf = Application::getInstance()->getConfiguration();
        $project_ext = $conf->load('ext');
        $temp = [];

        foreach ($project_ext['enabled'] as $pext) {
            if ($pext !== $ext) {
                $temp[] = $pext;
            }
        }

        $project_ext['enabled'] = array_unique($temp);
        natcasesort($project_ext['enabled']);

        if (count($project_ext['enabled'])) {
            $project_ext = Yaml::dump($project_ext);
        } else {
            $project_ext = 'enabled: []';
        }

        $ok = file_put_contents(
            Configuration::generateFileFilderFilePath('ext'),
            $project_ext
        );

        if ($ok) {
            $out->coutln('Successfully disabled {{ bold }}{{ purple }}%s{{ end }}', $ext);
        } else {
            $out->coutln('There war an error disabling {{ bold }}{{ purple }}%s{{ end }}', $ext);
        }

        return $ok;
    }

    /**
     * install an extension
     * @param string $ext
     * @return boolean
     */
    public static function install($ext)
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
