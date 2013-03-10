<?php

namespace Fabrico\Test;

use Fabrico\Request\Router;
use Fabrico\Request\Rule;

class RouterTest extends Test {
	public function testOneRulesCanBeAdded() {
		$rule = new Rule;
		$parser = new Router;
		$parser->addRule($rule);
		$this->assertEquals([ $rule ], $parser->getRules());
	}
}
