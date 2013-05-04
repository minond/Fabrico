<?php

namespace Fabrico\Request;

/**
 * represents a routing rule. for example, you could define how a requested
 * resource is handled, such as: which controller it should use, any sort of
 * request method limitations, request formats, etc.
 */
class Rule
{
    /**
     * request url pattern
     * @var string
     */
    private $pattern;

    /**
     * patter setter
     * @param string $patter
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * patter getter
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }
}
