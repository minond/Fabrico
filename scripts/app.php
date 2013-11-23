<?php

namespace Fabrico\Runtime\Setup;

use Fabrico\Application;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;

// overwriten by .fabrico file
getenv('FABRICO_APP_CONFIG') ?: putenv('FABRICO_APP_CONFIG=development');
getenv('FABRICO_APP_ENV') ?: putenv('FABRICO_APP_ENV=development');

// from scripts to root of project
if (getcwd() === __dir__) {
    chdir('..');
}

// environment overwrites
if (file_exists('.fabrico')) {
    require '.fabrico';
}

return call_user_func(function() {
    require 'vendor/autoload.php';
    $app = new Application;

    $conf = new Configuration;
    $conf->setCache(new RuntimeCache);
    $conf->setFormat(Configuration::YAML);
    $conf->setDirectory('config');
    $conf->setEnvironments(array_map('trim', array_filter(
        explode(',', getenv('FABRICO_APP_CONFIG') ?: '')
    )));

    $app->setConfiguration($conf);
    $app->initialize('config');
    $app->initialize('config_macros');

    return Application::bind($app);
});

