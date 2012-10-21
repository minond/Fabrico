<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Module;
use fabrico\core\util;

/**
 * fabrico template builder
 */
class Build extends Module {
	const VIEW = 'view';
	const TEMPLATE = 'template';
	
	
}


$bld = new Build;
util::dpre($bld->getc());
