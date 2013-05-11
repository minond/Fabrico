<?php

use Fabrico\Cache\ReadOnceCache;

require 'app.php';
require 'help.php';

$out = new TerminalOutput;
$em = new Fabrico\Core\ExtensionManager($conf);
$action = $argv[1];
$ext = $argv[2];
$conf->setCache(new ReadOnceCache);

switch ($action) {
    case 'install':
        install($ext, $out, $em, $conf);
        break;

    case 'disable':
        disable($ext, $out, $em);
        break;

    case 'enable':
        enable($ext, $out, $em);
        break;

    case 'enabled':
        enabled($ext, $out, $em);
        break;

    default:
        $out->coutln('Invalid action {{ error }}%s{{ end }}', $action);
        exit -1;
}
