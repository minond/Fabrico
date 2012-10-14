<?php

/**
 * class mediator
 * @package fabrico\core
 */

namespace fabrico\core;

/**
 * mediator class
 */
class Module {
	public function __get ($var) {
		switch ($var) {
			case 'core':
				return Core::instance();
		}
	}
}

/**
 * mediator functions
 */
trait Mediator {
	public function __get ($var) {
		switch ($var) {
			case 'core':
				return Core::instance();
		}
	}
}
