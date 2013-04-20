<?php

namespace Fabrico\Test\Mock\Response\Handler\Http;

use Fabrico\Response\Handler\Http\InvalidRequestHandler;

class PublicConfigurationHandler extends InvalidRequestHandler
{
    private $props = [];

    public function setPropertyValue($key, $val)
    {
        $this->props[ $key ] = $val;
    }

    public function getPropertyValue($key)
    {
        return $this->props[ $key ];
    }
}
