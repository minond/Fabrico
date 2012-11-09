<?php

namespace fabrico\core;

use fabrico\page\Page;
use fabrico\page\View;
use fabrico\page\Build;

// add page module to the response, view and build
core::instance()->response->outputcontent = new Page;
core::instance()->response->outputcontent->view = new View;
core::instance()->response->outputcontent->view->builder = new Build;
