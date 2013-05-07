<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Output\Cli\Output;
use Fabrico\Project\Configuration;
use Fabrico\Cache\RuntimeCache;
use Fabrico\Core\ExtensionManager;
use Fabrico\Event\Listeners;
use Symfony\Component\Yaml\Yaml;

function mklink($local, $newloc)
{
    if (file_exists($newloc)) {
        unlink($newloc);
    }

    return symlink($local, $newloc);
}

function install($ext, $out, $conf)
{
    $found_ext = false;
    $listeners = [];
    $pattern = FABRICO_ROOT . 'ext' . DIRECTORY_SEPARATOR .
        '*/configuration.yaml';

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

                    if (mklink($local, $newloc)) {
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

                    if (is_array($project_listeners)) {
                        $project_listeners = [];
                    }

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
            if (isset($config['config'])) {
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
            }

            // enable extension
            $out->coutln('{{ section }}Enabling extension{{ end }}');
            $ext_config = $conf->load('ext');

            if (!isset($ext_config['installed'])) {
                $ext_config['installed'] = [];
            }

            if (!isset($ext_config['enabled'])) {
                $ext_config['enabled'] = [];
            }

            $ext_config['installed'][] = $ext;
            $tmp = array_unique($ext_config['installed']);
            $ext_config['installed'] = [];
            foreach ($tmp as $e) {
                $ext_config['installed'][] = $e;
            }

            $ext_config['enabled'][] = $ext;
            $tmp = array_unique($ext_config['enabled']);
            $ext_config['enabled'] = [];
            foreach ($tmp as $e) {
                $ext_config['enabled'][] = $e;
            }

            $extfile = Configuration::generateFileFilderFilePath('ext');

            if (file_put_contents($extfile, Yaml::dump($ext_config, 100, 2)) !== false) {
                $out->cout('{{ space }}{{ ok }}');
            } else {
                $out->cout('{{ space }}{{ fail }}');
            }

            $out->coutln(' Adding %s', $extfile);

            $out->coutln('{{ eol }}Successfully installed {{ bold }}{{ purple }}%s{{ end }} extension!', $ext);
            $found_ext = true;
            break;
        }
    }

    if (!$found_ext) {
        $out->coutln('Extension {{ error }}%s{{ end }} not found!', $ext);
    }

    return true;
}

function disable($ext, $out, $em)
{
    if ($em->disable($ext)) {
        $out->coutln('Successfully disabled {{ bold }}{{ purple }}%s{{ end }}', $ext);
    } else {
        $out->coutln('Cound not disable {{ bold }}{{ purple }}%s{{ end }}', $ext);
    }
}

function enable($ext, $out, $em)
{
    if ($em->enable($ext)) {
        $out->coutln('Successfully enabled {{ bold }}{{ purple }}%s{{ end }}', $ext);
    } else {
        $out->coutln('Cound not enable {{ bold }}{{ purple }}%s{{ end }}', $ext);
    }
}

function enabled($ext, $out, $em)
{
    if ($em->enabled($ext)) {
        $out->coutln('{{ bold }}{{ purple }}%s{{ end }} is enabled', $ext);
    } else {
        $out->coutln('{{ bold }}{{ purple }}%s{{ end }} is not enabled', $ext);
    }
}

call_user_func(function() {
    global $argv;
    list(, $action, $ext) = $argv;

    $out = new Output;
    $app = new Application;
    $conf = new Configuration(new RuntimeCache);
    $em = new ExtensionManager($conf);

    $app->setConfiguration($conf);
    $app->setRoot(FABRICO_PROJECT_ROOT);
    $app->setNamespace($conf->get('project:namespace'));

    switch ($action) {
        case 'install':
            install($ext, $out, $conf);
            break;

        case 'disable':
            disable($ext, $out, $em);
            break;

        case 'enable':
            enable($ext, $out, $em);
            break;

        case 'enabled':
            enabled($ext, $out, $em);
            break;

        default:
            $out->coutln('Invalid action {{ error }}%s{{ end }}', $action);
            exit -1;
    }
});
