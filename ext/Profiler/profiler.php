<?php

use Fabrico\Core\Ext;
use util\profile\Profiler;
use util\profile\Snapshot;
use util\profile\reports\Chart;

if (Ext::enabled('profiler') && Profiler::enabled()) {
    $profiler = new Profiler('Project Profiler');
    $profiler->start();
    $profiler->on('stop', function(Snapshot $snapshot) {
        $chart = new Chart;
        $chart->prepare($snapshot);
        $chart->configure(['chart_type' => 'LineChart']);
        echo $chart->output();
    });
}
