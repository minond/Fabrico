<?php

// router for php built in server
require 'clioutput.php';

// routing info
$uri = $_SERVER['REQUEST_URI'];
CLI::stdout("%s $uri", [CLI::yellow('ROUTING')]);

// static resource?
if (strpos($uri, '/public') === 0 || strpos($uri, '/app/assets') === 0) {
    // for file types php can handle out of the box see documentation
    // http://php.net/manual/en/features.commandline.webserver.php
    CLI::stdout("%s $uri", [CLI::green('RESOURCE')]);
    return false;
} else {
    $res = require 'scripts/http.php';
    $ret = $res->getStatusCode();
    CLI::stdout("%s [%s] $uri", [CLI::green('PROCESSED'), $ret]);
}
