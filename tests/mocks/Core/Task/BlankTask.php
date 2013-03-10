<?php

namespace Fabrico\Test\Mock\Core;

use Fabrico\Core\Task;

class BlankTask implements Task {
	public function valid() {
		return true;
	}

	public function trigger() {
		return true;
	}
}
