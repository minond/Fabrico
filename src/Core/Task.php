<?php

namespace Fabrico\Core;

/**
 * represents a task that may be part of any job
 */
interface Task {
	/**
	 * returns true of Task is ready to be executed
	 * @return boolean
	 */
	public function valid();

	/**
	 * runs the task. returns success
	 * @return boolean
	 */
	public function trigger();
}
