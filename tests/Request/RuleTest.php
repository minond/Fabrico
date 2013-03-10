<?php

namespace Fabrico\Test;

use Fabrico\Request\Rule;

class RuleTest extends Test {
	public function testPatterStringsCanBeSetAndRetrieved() {
		$rule = new Rule;
		$rule->setPattern('/users');
		$this->assertEquals('/users', $rule->getPattern());
	}
}
