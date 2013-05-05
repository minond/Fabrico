<?php

use Fabrico\Event\Reporter;

call_user_func(function() {
    $here = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
    array_pop($here); // scripts
    array_pop($here); // src

    define('FABRICO_NAMESPACE', 'Fabrico');
    define('FABRICO_EXTENSION', '.php');
    define('FABRICO_ROOT', implode(DIRECTORY_SEPARATOR, $here) .
        DIRECTORY_SEPARATOR);
    array_pop($here); // Fabrico
    define('FABRICO_PROJECT_ROOT', implode(DIRECTORY_SEPARATOR, $here) .
        DIRECTORY_SEPARATOR);
    define('FABRICO_SRC', FABRICO_ROOT . 'lib' . DIRECTORY_SEPARATOR);
    define('FABRICO_BIN', FABRICO_ROOT . 'bin' . DIRECTORY_SEPARATOR);
    define('FABRICO_BIN_SRC', FABRICO_ROOT . 'bin' . DIRECTORY_SEPARATOR . 'lib' .
        DIRECTORY_SEPARATOR);
});

// everything should always be triggered from Fabrico's root directory
chdir(FABRICO_ROOT);
require 'vendor/autoload.php';

// and create Fabrico's own autoload
spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    $root = $parts[0];
    $rest = implode(DIRECTORY_SEPARATOR, array_slice($parts, 1));

    if ($root === FABRICO_NAMESPACE) {
        $file = $rest . FABRICO_EXTENSION;
        $in_bin = FABRICO_BIN_SRC . $file;
        $in_src = FABRICO_SRC . $file;
        $src = null;

        if (file_exists($in_bin)) {
            $src = $in_bin;
        } else if (file_exists($in_src)) {
            $src = $in_src;
        }

        if (!is_null($src)) {
            require $src;

            if (class_exists($class)) {
                Reporter::greet($class);
            }
        }
    }
}, true, true);
