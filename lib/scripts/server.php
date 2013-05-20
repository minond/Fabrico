<?php

/**
 * TODO: should handle resources
 */
namespace Fabrioc\Server\Handler;

use DateTime;

function writeln($msg, $time = false, $nl = false)
{
    $std = fopen('php://stdout', 'w');

    if ($time) {
        $time = microtime(true);
        $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
        $datetime = new DateTime(date('Y-m-d H:i:s.' . $micro, $time));

        fputs($std, "[");
        fputs($std, `tput setaf bold`);
        fputs($std, `tput setaf 3`);
        fputs($std, $datetime->format('Y-m-d H:i:s.u'));
        fputs($std, `tput sgr0`);
        fputs($std, "] ");
    }

    fputs($std, $msg);

    if ($nl) {
        fputs($std, "\n");
    }

    fclose($std);
}

function memory_info()
{
    global $sent_goodbye, $startmem, $starttime;

    $endtime = microtime(true);
    $endmem = memory_get_usage();

    $totaltime = $endtime - $starttime;
    $totalmem = $endmem - $startmem;
    $totalmem = $totalmem / 1024 / 1024;

    $totaltime = number_format($totaltime, 5);
    $totalmem = number_format($totalmem, 5);

    if (!$sent_goodbye) {
        $sent_goodbye = true;
        writeln(", memory: {$totalmem} MB, time: {$totaltime} µs", false, true);
    }
}

$uri = substr($_SERVER['SCRIPT_NAME'], 1);
$sent_goodbye = false;
$starttime = microtime(true);
$startmem = memory_get_usage();

// black list
if (in_array($uri, ['favicon.ico'])) {
    die;
}

writeln("uri: {$uri}", true, false);
$_REQUEST['_uri'] = $uri;
register_shutdown_function(__NAMESPACE__ . '\memory_info');
require 'lib/scripts/http.php';
memory_info();
