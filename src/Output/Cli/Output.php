<?php

namespace Fabrico\Output\Cli;

use Fabrico\Output\Output as OutputBase;

/**
 * terminal output
 */
class Output implements OutputBase
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
     * merge field adder
     * @param string $name
     * @param string $value
     */
    public static function mergefield($name, $value)
    {
        self::$mergefields[ $name ] = $value;
    }

    /**
     * apply merge fields
     * @inheritdoc
     */
    public function output()
    {
        if (!self::$mergefields_loaded) {
            self::$mergefields_loaded = require_once __DIR__ .
                DIRECTORY_SEPARATOR . 'mergefields.php';
        }

        // prepare string
        foreach (static::$mergefields as $name => $val) {
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
     * @inheritdoc
     */
    public function append($text)
    {
        $this->content = $this->content . $text;
    }

    /**
     * @inheritdoc
     */
    public function prepend($text)
    {
        $this->content = $text . $this->content;
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
