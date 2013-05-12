<?php

use Fabrico\Output\BasicOutput;
use Fabrico\Output\Output;
use Fabrico\Project\Configuration;
use Fabrico\Event\Listeners;

/**
 * terminal output
 */
class TerminalOutput extends BasicOutput implements Output
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
     * ask a question
     * @param array|string $msg
     * @param array $answer_map
     * @param callable $formatter
     * @return mixed
     */
    public function ask($msg, array $answer_map, $formatter = null)
    {
        if (is_array($msg)) {
            $msg = call_user_func_array('sprintf', $msg);
        }

        do {
            $this->cout("{$msg} ");
            $in = trim(fgets(STDIN));

            if (is_callable($formatter)) {
                $in = call_user_func($formatter, $in);
            }
        } while (!isset($answer_map[ $in ]));

        return $answer_map[ $in ];
    }

    /**
     * ask a yes/no question
     * @param array|string $msg
     * @return boolean
     */
    public function yesno($msg)
    {
        $options = ' [yes/no]';
        if (is_array($msg)) {
            $msg[0] = $msg[0] . $options;
        } else {
            $msg = $msg . $options;
        }

        return $this->ask($msg, [
            'y' => true,
            'yes' => true,
            'n' => false,
            'no' => false,
        ], 'strtolower');
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

function install($ext, $out, $em, $conf)
{
    $found_ext = false;
    $listeners = [];
    $pattern = FABRICO_ROOT . 'ext' . DIRECTORY_SEPARATOR .
        '*/configuration.yaml';

    foreach (glob($pattern) as $conffile) {
        $config = Configuration::parse($conffile);

        // found it
        if ($config['name'] === $ext) {
            $confdir = dirname($conffile) . DIRECTORY_SEPARATOR;
            $out->coutln('Installing {{ bold }}{{ purple }}%s{{ end }} extension', $ext);

            // check enviroment requirements
            if (isset($config['env'])) {
                foreach ($config['env'] as $check) {
                    $ok = false;

                    // php source file
                    if (isset($check['source'])) {
                        $ok = require $confdir . $check['source'];
                    }

                    if ($ok) {
                        $out->coutln('{{ ok }} ' . $check['messages']['pass']);
                    } else {
                        $out->coutln('{{ fail }} ' . $check['messages']['fail']);
                    }

                    if (!$ok && isset($check['required']) && $check['required']) {
                        $out->coutln('Stoping installation');
                        return false;
                    }
                }
            }

            // check deps
            if (isset($config['deps'])) {
                foreach ($config['deps'] as $dep) {
                    switch ($dep['type']) {
                        case 'extension':
                            $dname = $dep['name'];

                            if (!$em->enabled($dname)) {
                                if ($out->yesno(['Extension {{ bold }}{{ purple }}%s{{ end }} is a dependency, should I install it now?', $dname]) === true) {
                                    if (!install($dname, $out, $em, $conf)) {
                                        $out->coutln('Error installing {{ bold }}{{ purple }}%s{{ end }}', $dname);
                                        return false;
                                    }
                                } else {
                                    $out->coutln('Stopping installation');
                                    return false;
                                }
                            }

                            break;
                    }
                }
            }

            $dir = dirname($conffile) . DIRECTORY_SEPARATOR;

            // move source files
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
                        $out->cout('{{ ok }}');
                    } else {
                        $out->cout('{{ fail }}');
                    }

                    $out->coutln(' Moving %s -> %s ', $file, $newloc);
                }
            }

            // enable listeners, if any
            if (count($listeners)) {
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
                    $project_listeners = Configuration::dump($project_listeners);

                    if (file_put_contents($listeners_file, $project_listeners) !== false) {
                        $out->cout('{{ ok }}');
                    } else {
                        $out->cout('{{ fail }}');
                    }

                    $out->coutln(' Enabling %s', $listener);
                }
            }

            // add configuration
            if (isset($config['config'])) {
                $project_config = $config['config'];
                $project_config = Configuration::dump($project_config);
                $project_file = Configuration::generateFileFilderFilePath("ext/{$ext}");

                if (file_put_contents($project_file, $project_config) !== false) {
                $out->cout('{{ ok }}');
                } else {
                $out->cout('{{ fail }}');
                }

                $out->coutln(' Created %s', $project_file);
            }

            // enable extension
            if ($em->enable($ext)) {
                $out->cout('{{ ok }}');
            } else {
                $out->cout('{{ fail }}');
            }

            $extfile = Configuration::generateFileFilderFilePath('ext');
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
