<?php

namespace fabrico\core;

use fabrico\page\Page;
use fabrico\page\View;
use fabrico\page\Build;

// add page module to the response, view and build
core::instance()->response->page = new Page;
core::instance()->response->page->view = new View;
core::instance()->response->page->view->builder = new Build;
