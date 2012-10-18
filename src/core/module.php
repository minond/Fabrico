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
	const __PROP_NAME = 'core';

	public function __get ($var) {
		\fabrico\core\util::dpre($var);
		switch ($var) {
			case self::__PROP_NAME:
				return $this->getc();
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
			case Module::__PROP_NAME:
				return $this->getc();
		}
	}

	public function getc () {
		return Core::instance();
	}
}
