<?php

namespace fabrico\core;

use fabrico\output\Page;
use fabrico\output\View;
use fabrico\output\Build;

// add page module to the response, view and build
core::instance()->response->outputcontent = new Page;
core::instance()->response->outputcontent->view = new View;
core::instance()->response->outputcontent->view->builder = new Build;
