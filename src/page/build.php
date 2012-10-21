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
	/**
	 * file types
	 */
	const VIEW = 'view';
	const TEMPLATE = 'template';
	
	/**
	 * @return boolean
	 */
	public function can_build () {
		return $this->configuration->core->templates->build;
	}
}
