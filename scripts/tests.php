<?php

putenv('APP_ENV=test');
require 'vendor/autoload.php';
Fabrico\Runtime\Instance::create();
