<?php

namespace Fabrico\Core;

class BlankTask implements Task {
	public function valid() {
		return true;
	}

	public function trigger() {
		return true;
	}
}
