<?php

namespace Fabrico;

interface Initializer
{
    /**
     * called on the start of an action
     * @return mixed
     */
    public function initialize();

    /**
     * called on the end of an action
     */
    public function terminate();
}
