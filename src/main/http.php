<?php

namespace fabrico\core;

require '../core/core.php';
require '../core/module.php';
require '../core/util.php';
require '../loader/loader.php';
require '../loader/core.php';
require '../loader/deps.php';
require 'deps.php';
require 'core.php';


use fabrico\logz\Logz;
use fabrico\logz\handler\FileHandler;
core::instance()->core->load('log');

$log = new Logz('Testing');
$log->add_handler(new FileHandler(Logz::INFO, 'out.log'));
$log->information('test');

util::dpre($log);
die;

// route the request
switch (true) {
	case core::instance()->router->is_view:
		// load page related modules and initialize them
		core::instance()->core->load('page');
		require 'view.php';

		// load the view file
		core::instance()->response->page->get(core::instance()->request->file);
		break;
	
	default:
		core::instance()->response->addheader(\fabrico\core\Response::HTTP404);
		break;
}

core::instance()->response->reply();
