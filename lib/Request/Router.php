<?php

namespace Fabrico\Request;

/**
 * manages a list of routing rules, find the best one to handle current
 * request, and routes the request to the correct view.
 */
class Router
{
    /**
     * list of route rules
     * @var Rule[]
     */
    private $rules = [];

    /**
     * rule adder
     * @param Rule $rule
     */
    public function addRule(Rule & $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * returns all rules
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }
}
