<?php

$uri = $_SERVER['REQUEST_URI'];
$headers = [
    'css' => [ 'Content-Type' => 'text/css' ],
    'js' => [ 'Content-Type' => 'application/javascript' ],
];

// resource?
if (strpos($uri, '/public') === 0) {
    $ext = explode('.', $uri);
    $ext = array_pop($ext);

    // do we have headers for this file type?
    if (isset($headers[ $ext ])) {
        foreach ($headers[ $ext ] as $header => $value) {
            header("$header: $value");
        }
    }

    echo file_get_contents(".$uri");
} else {
    chdir('scripts');
    require 'http.php';
}

