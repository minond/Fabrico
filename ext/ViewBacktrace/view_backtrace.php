<?php

use Fabrico\Event\Listener;
use Fabrico\Event\Reporter;
use Fabrico\View\View;
use Fabrico\Core\Ext;

if (Ext::enabled('view_backtrace')) {
    Reporter::observe(
        'Fabrico\Request\Http\Request',
        'prepareHandler',
        Listener::PRE,
        function($info) {
            $view     = Ext::config('view_backtrace:view');
            $errors   = Ext::config('view_backtrace:error:reporting');
            $err_msg  = Ext::config('view_backtrace:error:label');
            $err_kill = Ext::config('view_backtrace:error:kill');
            $exc_kill = Ext::config('view_backtrace:exception:kill');
            $bak_show = Ext::config('view_backtrace:backtrace:display');
            $src_show = Ext::config('view_backtrace:source:display');
            $src_line = Ext::config('view_backtrace:source:line_offset');

            error_reporting($errors);

            set_error_handler(function($errnum, $message, $file, $line) use (
                $view,
                $err_msg,
                $err_kill,
                $bak_show,
                $src_show,
                $src_line
            ) {
                $errtype = array_key_exists($errnum, $err_msg) ?
                    $err_msg[ $errnum ] : $errnum;

                echo View::generate($view, [
                    'errtype' => $errtype,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line,
                    'backtrace' => debug_backtrace(),
                    'display_backtrace' => $bak_show,
                    'display_source' => $src_show,
                    'source' => getsource($file, $line, $src_line, $src_show),
                ]);

                if ($err_kill) {
                    die;
                }
            }, $errors);

            set_exception_handler(function($exception) use (
                $view,
                $exc_kill,
                $bak_show,
                $src_show,
                $src_line
            ) {
                $backtrace = $exception->getTrace();
                $file = $backtrace[0]['file'];
                $line = $backtrace[0]['line'];

                // prepend exception thrown location
                array_unshift($backtrace, [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);

                echo View::generate($view, [
                    'errtype' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file' => $file,
                    'line' => $line,
                    'backtrace' => $backtrace,
                    'display_backtrace' => $bak_show,
                    'display_source' => $src_show,
                    'source' => getsource($file, $line, $src_line, $src_show),
                ]);

                if ($exc_kill) {
                    die;
                }
            });

            /**
             * source contents getter
             * @param string file
             * @param int $line
             * @param int $offset - optional, default = 10
             * @param int $show - optional, default = true
             * @return string
             */
            function getsource($file, $line, $offset = 10, $show = true)
            {
                $source = [];
                $lines = null;

                if ($show) {
                    $lines = explode(PHP_EOL, file_get_contents($file));
                    $i = $line - $offset;
                    $max = $line + $offset + 1;

                    for (; $i < $max; $i++) {
                        if (isset($lines[ $i - 1 ])) {
                            $source[] = [
                                'text' => $lines[ $i - 1 ],
                                'num' => $i,
                            ];
                        }
                    }
                }

                return $source;
            }
        }
    );
}
