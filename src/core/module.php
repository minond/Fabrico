<?php

/**
 * class mediator
 * though Mediator and Module both act as mediators
 * between all Fabrico modules, the real mediator is
 * the 'core' class as it has control over module
 * access. Mediator and Module act as mediators for
 * mainly the core and configuration.
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * mediator functions
 */
trait Mediator {
	/**
	 * additional property shortcuts can be added
	 * as long as they all rely on Mediator::getc
	 * @see self::getc
	 * @return mixed
	 */
	public function __get ($var) {
		switch ($var) {
			case 'core':
				return $this->getc();

			case 'configuration':
				return $this->getc()->configuration;
		}
	}

	/**
	 * @see self::getcore
	 * @return core
	 */
	public function & getc () {
		return self::getcore();
	}

	/**
	 * @return core
	 */
	protected static function & getcore () {
		return Core::instance();
	}
}

/**
 * mediator class
 */
abstract class Module {
	/**
	 * should be no different than the Mediator trait
	 */
	use Mediator;
}
