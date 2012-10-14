<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * event listener
 */
class EventDispatch {
	/**
	 * event lineters
	 * @var array
	 */
	private $listeners = [];

	/**
	 * removes all event listeners
	 */
	public function unbind_all () {
		$this->listeners = [];
	}

	/**
	 * removes a whole namespace
	 * @param string $namespace
	 */
	public function unbind_ns ($namespace) {
		unset($this->listeners[ $namespace ]);
	}

	/**
	 * removes a subset of a namespace
	 * @param string $namespace
	 * @param string $event
	 */
	public function unbind_events ($namespace, $event) {
		unset($this->listeners[ $namespace ][ $event ]);
	}

	/**
	 * removes an event listener
	 * @param string $id
	 */
	public function unbind ($id) {
		foreach ($this->listeners as $namespace => $subset) {
			foreach ($subset as $event_subset => $events) {
				foreach ($events as $event_index => $event) {
					if ($event->id === $id) {
						unset($this->listeners[ $namespace ][ $event_subset ][ $event_index ]);
						break;
					}
				}
			}
		}
	}

	/**
	 * add an event listener
	 * @param string $namespace
	 * @param string $event
	 * @param callable $action
	 * @return event id
	 */
	public function bind ($namespace, $event, $action) {
		$storage = new \stdClass;

		if (!isset($this->listeners[ $namespace ])) {
			$this->listeners[ $namespace ] = [];
		}

		if (!isset($this->listeners[ $namespace ][ $event ])) {
			$this->listeners[ $namespace ][ $event ] = [];
		}

		$storage->action = $action;
		$storage->id = uniqid();
		$this->listeners[ $namespace ][ $event ][] = $storage;

		return $storage->id;
	}

	/**
	 * fire event listeners
	 * @param string $namespace
	 * @param string $event
	 * @param array $args
	 */
	public function trigger ($namespace, $event, $args = []) {
		if (isset($this->listeners[ $namespace ][ $event ])) {
			foreach ($this->listeners[ $namespace ][ $event ] as $event_info) {
				call_user_func_array($event_info->action, $args);
			}
		}
	}
}
