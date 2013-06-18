<?php

call_user_func(function() {
    require 'app.php';

    while (true) {
        echo 'fabrico> ';
        $command = trim(fgets(STDIN));
        $output = '';

        switch ($command) {
            case 'exit':
                exit(0);
                break;

            case 'clear':
                echo str_repeat("\n", 100);
                break;

            default:
                ob_start();

                try {
                    $output = trim(eval($command . ';'));
                } catch (\Exception $error) {
                    $output = 'Exception: ' . $error->getMessage();
                }

                $output = ob_get_clean() . $output;
                break;
        }

        if ($output) {
            echo $output . PHP_EOL;
        }
    }
});
