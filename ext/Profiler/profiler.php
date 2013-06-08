<?php

use Fabrico\Core\Ext;
use util\profile\Profiler;
use util\profile\Snapshot;
use util\profile\reports\Chart;

if (class_exists('Profiler') && Ext::enabled('profiler') && Profiler::enabled()) {
    $profiler = new Profiler(
        Ext::config('profiler:label'),
        Ext::config('profiler:mode')
    );

    $profiler->on('stop', function(Snapshot $snapshot) {
        $class = Ext::config('profiler:output:class');
        $confi = Ext::config('profiler:output:settings');

        $out = new $class;
        $out->prepare($snapshot);

        if (is_array($confi) && count($confi)) {
            $out->configure($confi);
        }

        echo $out->output();
    });

    $profiler->start();
}
