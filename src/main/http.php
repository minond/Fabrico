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

// route the request
switch (true) {
	case core::instance()->router->is_view:
		core::instance()->core->load('page');
		require 'view.php';
		core::instance()->response->page->get(core::instance()->request->file);
		break;
	
	default:
		core::instance()->response->addheader(\fabrico\core\Response::HTTP404);
		break;
}

core::instance()->response->reply();
