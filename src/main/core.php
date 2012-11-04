<?php

namespace fabrico\core;

use fabrico\core\Project;
use fabrico\core\Reader;
use fabrico\core\EventDispatch;
use fabrico\core\Request;
use fabrico\core\Router;
use fabrico\core\Response;
use fabrico\configuration\Configuration;

// base modules
core::instance()->project = new Project;
core::instance()->reader = new Reader;
core::instance()->event = new EventDispatch;

// load framework configuration
core::instance()->configuration = new Configuration;
//core::instance()->configuration->clear(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);
core::instance()->configuration->load(Configuration::CORE, Configuration::HTTPCONF, Configuration::APC);

// request handlers
core::instance()->request = new Request;
core::instance()->router = new Router($_REQUEST);
core::instance()->response = new Response;

// always default to an HTML response
core::instance()->response->as = Response::HTML;
