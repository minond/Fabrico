<?php

class CLI
{
    /**
     * cli output
     * format: [Sat Dec 21 21:54:56 2013] addr:port Invalid request (...)
     * @param string $msg
     * @param array $args, merge string aruments (for sprintf)
     * @return int|boolean, number of bytes writen, or false on error
     */
    public static function stdout($msg, array $args = [])
    {
        $stdout = fopen('php://stdout', 'w');
        $tmpl = "[%s] %s:%s %s\n";

        $date = date(sprintf('D M %2s H:i:s Y', date('j')));
        $addr = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];

        // new message
        array_unshift($args, $msg);
        $msg = call_user_func_array('sprintf', $args);
        fwrite($stdout, sprintf($tmpl, $date, $addr, $port, $msg));
    }

    /**
     * return a yellow string
     * @param string $str
     * @return string
     */
    public static function yellow($str)
    {
        return self::colorstr('1;33', $str);
    }

    /**
     * return a yellow string
     * @param string $str
     * @return string
     */
    public static function green($str)
    {
        return self::colorstr('1;32', $str);
    }

    /**
     * return a colored string string
     * @param string $color
     * @param string $str
     * @return string
     */
    public static function colorstr($color, $str)
    {
        return sprintf("\033[%sm%s\033[0m", $color, $str);
    }
}
