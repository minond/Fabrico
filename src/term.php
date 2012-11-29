<?php

/**
 * standard handling of http requests
 */
namespace fabrico;

use fabrico\core\Core;
use fabrico\controller\Controller;

require 'main.php';

Core::run(function (Core $app) {
	global $argv;
	print_r($argv);
	echo 'running...';
});
