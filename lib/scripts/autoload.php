<?php

use Fabrico\Event\Reporter;

call_user_func(function() {
    $ds = DIRECTORY_SEPARATOR;
    $here = explode($ds, dirname(__FILE__));
    array_pop($here); // scripts
    array_pop($here); // src

    // Fabrico directories
    define('FABRICO_NAMESPACE', 'Fabrico');
    define('FABRICO_EXTENSION', '.php');
    define('FABRICO_ROOT', implode($ds, $here) . $ds);
    define('FABRICO_SRC', FABRICO_ROOT . 'lib' . $ds);
    array_pop($here); // Fabrico

    // project directories
    define('FABRICO_PROJECT_ROOT', implode($ds, $here) . $ds);

    // everything should always be triggered from the root directory
    chdir(FABRICO_ROOT);
    require 'vendor/autoload.php';
});

// Fabrico's own autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, FABRICO_NAMESPACE) === 0) {
        $part = explode('\\', $class);
        $rest = implode(DIRECTORY_SEPARATOR, array_slice($part, 1));
        $file = FABRICO_SRC . $rest . FABRICO_EXTENSION;

        if (file_exists($file)) {
            require_once $file;

            if (class_exists($class)) {
                Reporter::greet($class);
            }
        }
    }
}, true, true);
