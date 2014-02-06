<?php

// router for php built in server
require 'clioutput.php';

// routing info
$uri = $_SERVER['REQUEST_URI'];
stdout("%s $uri", [yellow('ROUTING')]);

// static resource?
if (strpos($uri, '/public') === 0) {
    // for file types php can handle out of the box see documentation
    // http://php.net/manual/en/features.commandline.webserver.php
    stdout("%s $uri", [green('RESOURCE')]);
    return false;
} else {
    $res = require 'scripts/http.php';
    $ret = $res->getStatusCode();
    stdout("%s [%s] $uri", [green('PROCESSED'), $ret]);
}
