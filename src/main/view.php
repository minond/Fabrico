<?php

namespace fabrico\core;

use fabrico\page\Page;
use fabrico\page\View;
use fabrico\page\Build;

core::instance()->response->page = new Page;
core::instance()->response->page->view = new View;
core::instance()->response->page->view->builder = new Build;
core::instance()->response->page->get(core::instance()->request->file);
