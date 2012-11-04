<?php

/**
 * class mediator
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * mediator functions
 */
trait Mediator {
	public function __get ($var) {
		switch ($var) {
			case 'core': return $this->getc();
			case 'configuration': return $this->getc()->configuration;
		}
	}

	public function & getc () {
		return self::getcore();
	}

	protected static function & getcore () {
		return core::instance();
	}
}

/**
 * mediator class
 */
abstract class Module { use Mediator; }
