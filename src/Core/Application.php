<?php

namespace Fabrico\Core;

use \Fabrico\Request\ApplicationRequest;

class Application {
	/**
	 * project root directory
	 * @var string
	 */
	private $root;

	/**
	 * current task
	 * @var Job
	 */
	private $job;

	/**
	 * project root setter
	 * @param string $root
	 */
	public function setRoot($root) {
		$this->root = $root;
	}

	/**
	 * project root setter
	 * @return string
	 */
	public function getRoot() {
		return $this->root;
	}

	/**
	 * job setter
	 * @param Job $job
	 */
	public function setJob( & $job) {
		return $this->job = $job;
	}

	/**
	 * job getter
	 * @return Job
	 */
	public function getJob() {
		return $this->job;
	}
}
