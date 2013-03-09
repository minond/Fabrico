<?php

namespace Fabrico\Test;

use Fabrico\Core\Application;
use Fabrico\Core\Job;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
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
