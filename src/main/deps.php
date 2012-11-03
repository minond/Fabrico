<?php

namespace fabrico\core;

use fabrico\loader\CoreLoader;
use fabrico\loader\DepsLoader;

core::instance()->core = new CoreLoader;
core::instance()->deps = new DepsLoader;
core::instance()->deps->set_path('../../../admin/php_include/');
