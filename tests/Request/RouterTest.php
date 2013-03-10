<?php

namespace Fabrico\Test\Request;

use Fabrico\Request\Router;
use Fabrico\Request\Rule;
use Fabrico\Test\Test;

class RouterTest extends Test {
	public function testOneRulesCanBeAdded() {
		$rule = new Rule;
		$parser = new Router;
		$parser->addRule($rule);
		$this->assertEquals([ $rule ], $parser->getRules());
	}
}
