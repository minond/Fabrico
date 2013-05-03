<?php

namespace Fabrico\Output\Cli;

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
