<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Project\Configuration;
use Fabrico\Cache\RuntimeCache;
use Fabrico\Core\ExtensionManager;
use Fabrico\Event\Listeners;
use Fabrico\Reader\Yaml;
use Fabrico\Output\BasicOutput;
use Fabrico\Output\Output as OutputBase;

/**
 * terminal output
 */
class Output extends BasicOutput implements OutputBase
{
    /**
     * merge fields already loaded flag
     * @var boolean
     */
    private static $mergefields_loaded = false;

    /**
     * merge fields: ie {red}I'm red{end}
     * @var array
     */
    protected static $mergefields = [];

    /**
     * merge fields for this instance
     * @var array
     */
    protected $instance_mergefields = [];

    /**
     * merge field format
     * @var string
     */
    private $mergefield_template = '{{ %s }}';

    /**
     * output content
     * @var string
     */
    protected $content;

    /**
     * sets standard merge fields
     */
    private static function loadMergeFields()
    {
        self::$mergefields['red'] = `tput setaf 1`;
        self::$mergefields['green'] = `tput setaf 2`;
        self::$mergefields['yellow'] = `tput setaf 3`;
        self::$mergefields['blue'] = `tput setaf 4`;
        self::$mergefields['purple'] = `tput setaf 5`;
        self::$mergefields['teal'] = `tput setaf 6`;
        self::$mergefields['white'] = `tput setaf 7`;
        self::$mergefields['error'] = `tput bold && tput setaf 1`;
        self::$mergefields['pass'] = `tput bold && tput setaf 2`;
        self::$mergefields['warn'] = `tput bold && tput setaf 3`;
        self::$mergefields['info'] = `tput bold && tput setaf 7`;
        self::$mergefields['notice'] = `tput sgr 0 1 && tput bold`;
        self::$mergefields['bold'] = `tput bold`;
        self::$mergefields['underline'] = `tput sgr 0 1`;
        self::$mergefields['end'] = `tput sgr0`;
        self::$mergefields['eol'] = PHP_EOL;
        self::$mergefields['backspace'] = chr(0x08);
        self::$mergefields['section'] = "\n    - " . `tput sgr 0 1 && tput bold`;
        self::$mergefields['space'] = '    ';
        self::$mergefields['tab'] = "\t";
        self::$mergefields['nl'] = "\n";
        self::$mergefields['ok'] = '[' . `tput bold && tput setaf 2` . 'ok' . `tput sgr0` . ']';
        self::$mergefields['fail'] = '[' . `tput bold && tput setaf 1` . 'fail' . `tput sgr0` . ']';

        self::$mergefields['time'] = function() {
            return (string) time();
        };

        self::$mergefields['rand'] = function() {
            return (string) rand();
        };

        self::$mergefields['clear'] = function() {
            passthru('clear');
            return '';
        };
    }

    /**
     * merge field adder
     * @param string $name
     * @param string $value
     */
    public function mergefield($name, $value)
    {
        $this->instance_mergefields[ $name ] = $value;
    }

    /**
     * apply merge fields
     * @inheritdoc
     */
    public function output()
    {
        if (!self::$mergefields_loaded) {
            self::loadMergeFields();
            self::$mergefields_loaded = true;
        }

        // prepare string
        foreach (array_merge(static::$mergefields, $this->instance_mergefields) as $name => $val) {
            $field = sprintf($this->mergefield_template, $name);

            if (strpos($this->content, $field) === false) {
                continue;
            }

            if (is_callable($val)) {
                $val = $val();
            }

            $this->content = str_replace($field, $val, $this->content);
        }

        // output it and reset it
        echo $this->content;
        $this->content = '';
    }

    /**
     * outputs a string
     * @param string $text
     * @param mixed $args*
     * @return void
     */
    public function cout($text, $args = null)
    {
        $this->append(call_user_func_array('sprintf', func_get_args()));
        $this->output();
    }

    /**
     * outputs a string with a eol character at the end of the string
     * @see Output::cout
     * @param string $text
     * @param mixed $args*
     * @return void
     */
    public function coutln($text, $args = null)
    {
        call_user_func_array([ $this, 'cout' ], func_get_args());
        $this->append(PHP_EOL);
        $this->output();
    }

    /**
     * append a backspace character
     */
    public function backspace()
    {
        $this->append(chr(0x08));
    }
}

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

                    if (!is_array($project_listeners)) {
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
                    $project_listeners = Yaml::dump($project_listeners);

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
                $project_config = Yaml::dump($project_config);
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

            if (file_put_contents($extfile, Yaml::dump($ext_config)) !== false) {
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
