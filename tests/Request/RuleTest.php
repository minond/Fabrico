<?php

namespace Fabrico\Test\Request;

use Fabrico\Request\Rule;
use Fabrico\Test\Test;

class RuleTest extends Test
{
    public function testPatterStringsCanBeSetAndRetrieved()
    {
        $rule = new Rule;
        $rule->setPattern('/users');
        $this->assertEquals('/users', $rule->getPattern());
    }
}
