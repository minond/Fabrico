<?php

/**
 * basic observer implementation
 * @package fabrico\observer
 */

namespace fabrico\observer;

/**
 * makes any property observable
 */
trait PublicObserver {
	/**
	 * observation tracker
	 * @var array
	 */
	private $observing = [];

	/**
	 * property observer
	 * @param string $prop
	 * @param callable $action
	 */
	public function observe ($prop, callable $action) {
		$this->observing[ $prop ] = $action;
	}

	/** 
	 * observer trigger
	 * @param string $prop
	 * @param mixed $value
	 */
	public function __set ($prop, $value) {
		if (array_key_exists($prop, $this->observing)) {
			$obs = new Observation;
			$obs->property = $prop;
			$obs->new = $value;
			$obs->old = $this->{ $prop };

			$this->observing[ $prop ]($obs);
		}

		$this->{ $prop } = $value;
	}
}
