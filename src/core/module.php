<?php

/**
 * class mediator
 * @package fabrico\core
 */

namespace fabrico\core;

/**
 * mediator class
 */
abstract class Module {
	const __CORE_NAME = 'core';
	const __CONF_NAME = 'configuration';

	public function __get ($var) {
		switch ($var) {
			case self::__CORE_NAME:
				return $this->getc();

			case self::__CONF_NAME:
				return $this->getc()->configuration;
		}
	}

	public function getc () {
		return Core::instance();
	}
}

/**
 * mediator functions
 */
trait Mediator {
	public function __get ($var) {
		switch ($var) {
			case Module::__CORE_NAME:
				return $this->getc();

			case self::__CONF_NAME:
				return $this->getc()->configuration;
		}
	}

	public function getc () {
		return Core::instance();
	}
}
