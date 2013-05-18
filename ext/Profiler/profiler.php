<?php

use util\profile\Profiler;
use util\profile\Snapshot;

$profiler = new Profiler('Project Profiler');

$profiler->on('stop', function(Snapshot $snapshot) {
    print_r($snapshot);
});

$profiler->start();
