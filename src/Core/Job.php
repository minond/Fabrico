<?php

namespace Fabrico\Core;

class Job {
	/**
	 * tasks in this job
	 * @var Task[]
	 */
	private $tasks = [];

	/**
	 * returnes the number of tasks
	 * @return int
	 */
	public function taskCount() {
		return count($this->tasks);
	}

	/**
	 * task adder
	 * @param Task $task
	 */
	public function addTask(Task & $task) {
		$this->tasks[] = $task;
	}

	/**
	 * tasks adder
	 * @param Task[] $tasks
	 */
	public function addTasks(array & $tasks) {
		foreach ($tasks as & $task) {
			$task->id = \uniqid();
			$this->addTask($task);
			unset($task);
		}
	}

	/**
	 * task checker
	 * @param Task $task
	 * @return boolean
	 */
	public function hasTask(Task & $task) {
		return in_array($task, $this->tasks);
	}

	/**
	 * task remover. returns true if task was removed
	 * @param Task $task
	 * @return boolean
	 */
	public function removeTask(Task & $task) {
		if ($this->hasTask($task)) {
			foreach ($this->tasks as $index => & $mtask) {
				if ($task === $mtask) {
					array_splice($this->tasks, $index, 1);
					break;
				}

				unset($mtask);
			}
		}
	}
}
