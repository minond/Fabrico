<?php

/**
 * class mediator
 * @package fabrico
 */

namespace fabrico;

class Module {
	use Mediator;
}

trait Mediator {
	public function __get ($var) {
		switch ($var) {
			case 'core':
				return Core::instance();
		}
	}
}
