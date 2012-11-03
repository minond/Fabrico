<?php

namespace fabrico\core;

use fabrico\core\Project;
use fabrico\core\Reader;
use fabrico\core\EventDispatch;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\core\Response;
use fabrico\configuration\Configuration;

// configuration
core::instance()->configuration = new Configuration;
core::instance()->configuration->load(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);

// modules
core::instance()->project = new Project;
core::instance()->reader = new Reader;
core::instance()->event = new EventDispatch;
core::instance()->request = new Request;
core::instance()->router = new Router($_REQUEST);
core::instance()->response = new Response;
core::instance()->response->as = Response::HTML;
