<?php

namespace Fabrico\Test;

use Fabrico\Core\Job;
use Fabrico\Core\Task;
use Fabrico\Core\BlankTask;

require 'tests/mocks/Core/Task/BlankTask.php';

class JobTest extends \PHPUnit_Framework_TestCase {
	public $job;
	public $task;
	public $tasks;
	public $tnum;

	public function setUp() {
		$this->job = new Job;
		$this->tasks = [
			new BlankTask,
			new BlankTask,
			new BlankTask,
			new BlankTask
		];
		$this->task = $this->tasks[ 2 ];
		$this->tnum = count($this->tasks);
	}

	public function testTasksCanBeAddedToAJob() {
		$this->job->addTask($this->task);
		$this->assertEquals(1, $this->job->taskCount());
	}

	public function testMultipleTasksCanBeAddedToAJob() {
		$this->job->addTasks($this->tasks);
		$this->assertEquals($this->tnum, $this->job->taskCount());
	}

	public function testTasksCanBeRemovedFromAJob() {
		$this->job->addTasks($this->tasks);
		$this->job->removeTask($this->task);
		$this->assertFalse($this->job->hasTask($this->task));
		$this->assertEquals($this->tnum - 1, $this->job->taskCount());
	}

	public function testTasksCanBeFound() {
		$this->job->addTasks($this->tasks);
		$this->assertTrue($this->job->hasTask($this->task));
	}
}
