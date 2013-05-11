<?php

/**
 * creates a new Fabrico Application.
 * loads project Listeners
 */

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Project\Configuration;
use Fabrico\Event\Listeners;
use Fabrico\Cache\RuntimeCache;

$app = new Application;
$conf = new Configuration(new RuntimeCache);

$app->setConfiguration($conf);
$app->setRoot(FABRICO_PROJECT_ROOT);
$app->setNamespace($conf->get('project:namespace'));

// project bootstraps
if (count($conf->get('project:bootstrap'))) {
    foreach ($conf->get('project:bootstrap') as $file) {
        require_once FABRICO_PROJECT_ROOT . $file;
    }
}

// listeners
if (count($conf->get('listeners'))) {
    $listeners = new Listeners;
    $listeners->setListeners($conf->get('listeners'));
    $listeners->loadListeners();
    unset($listeners);
}
