<?php

namespace Fabrico\Test\Core;

use Fabrico\Core\Application;
use Fabrico\Core\Job;
use Fabrico\Test\Test;

class ApplicationTest extends Test {
	public $app;

	public function setUp() {
		$this->app = new Application;
	}

	public function testRootCanBeSetAndRetrieved() {
		$this->app->setRoot('/');
		$this->assertEquals('/', $this->app->getRoot());
	}

	public function testJobsCanBeSetAndRetrieved() {
		$job = new Job;
		$this->app->setJob($job);
		$this->assertEquals($job, $this->app->getJob());
	}
}
