<?php

namespace Fabrico\Event;

/**
 * manages object subscriptions
 */
class Reporter {
	/**
	 * every subscription made to an object that does not exists yet is saved
	 * in a queue to be added as soon as the object greets the reporter
	 */
	private static $queue = [];

	/**
	 * removed the first namespace slash
	 * @param string $class
	 * @return string
	 */
	private static function cleanClassName($class) {
		return preg_replace('/^\\\/', '', $class);
	}

	/**
	 * when new classes are loaded they should greet the reporter which will
	 * then check the queue for any of their subscriptions
	 * @param string $class
	 * @throws \Exception
	 */
	public static function greet($class) {
		$clean_class = self::cleanClassName($class);

		if (!class_exists($class)) {
			throw new \Exception("Unknown class: {$class}");
		}

		foreach (self::$queue as $index => & $sub) {
			if ($sub->class === $clean_class) {
				$class::observe($sub->name, $sub->type, $sub->action);
				unset(self::$queue[ $index ]);
			}

			unset($sub);
		}
	}

	/**
	 * set subscriptions if object has been loaded or queues it.
	 * @param string $class
	 * @param string $name
	 * @param string $type
	 * @param mixed callable|Closure $action
	 */
	public function observe($class, $name, $type, $action) {
		if (!class_exists($class)) {
			self::$queue[] = (object) [
				'class' => self::cleanClassName($class),
				'name' => $name,
				'type' => $type,
				'action' => $action ];
		} else {
			$class::observe($name, $type, $action);
		}
	}
}
