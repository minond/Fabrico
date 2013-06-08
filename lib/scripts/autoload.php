<?php

use Fabrico\Event\Reporter;

call_user_func(function() {
    $ds = DIRECTORY_SEPARATOR;
    $here = explode($ds, dirname(__FILE__));
    array_pop($here); // scripts
    array_pop($here); // src

    // Fabrico directories
    define('FABRICO_DIRECTORY', $here[ count($here) - 1 ]);
    define('FABRICO_NAMESPACE', 'Fabrico');
    define('FABRICO_EXTENSION', '.php');
    define('FABRICO_MOCKS', 'Fabrico\Test\Mock');
    define('FABRICO_ROOT', implode($ds, $here) . $ds);
    define('FABRICO_SRC', FABRICO_DIRECTORY . $ds . 'lib' . $ds);
    array_pop($here); // Fabrico

    // project directories
    define('FABRICO_PROJECT_ROOT', implode($ds, $here) . $ds);

    // everything should always be triggered from the root directory
    chdir(FABRICO_PROJECT_ROOT);
    require FABRICO_ROOT . 'vendor/autoload.php';
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
        } else if (strpos($class, FABRICO_MOCKS) === 0) {
            // mock?
            $mock = str_replace(FABRICO_MOCKS, '', $class);
            $mock = explode('\\', $mock);
            $mock = implode(DIRECTORY_SEPARATOR, $mock);
            $mock = FABRICO_ROOT . 'tests/mocks' .
                $mock . FABRICO_EXTENSION;

            if (file_exists($mock)) {
                require_once $mock;
            }
        }
    }
}, true, true);
