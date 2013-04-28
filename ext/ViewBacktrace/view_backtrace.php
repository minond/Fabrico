<?php

use Fabrico\Event\Listener;
use Fabrico\Event\Reporter;
use Fabrico\View\View;

Reporter::observe('Fabrico\Request\Http\Request', 'prepareHandler', Listener::PRE,
    function($info) {
        error_reporting(E_ALL);

        set_error_handler(function($errnum, $message, $file, $line) {
            echo View::generate('view_backtrace.twig', [
                'errtype' => $errnum,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'backtrace' => debug_backtrace(),
                'source' => getsource($file, $line),
            ]);

            die;
        }, E_ALL);

        set_exception_handler(function($exception) {
            echo View::generate('view_backtrace.twig', [
                'errtype' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'backtrace' => $exception->getTrace(),
                'source' => getsource($exception->getFile(), $exception->getLine()),
            ]);

            die;
        });

        /**
         * source contents getter
         * @param string file
         * @param int $line
         * @param int $offset - optional, default = 9
         * @return string
         */
        function getsource($file, $line, $offset = 9)
        {
            $lines = explode(PHP_EOL, file_get_contents($file));
            $source = [];

            for ($i = $line - $offset, $max = $line + $offset + 1; $i < $max; $i++) {
                if (isset($lines[ $i - 1 ])) {
                    $source[] = [
                        'text' => $lines[ $i - 1 ],
                        'num' => $i,
                    ];
                }
            }

            return $source;
        }
    }
);
