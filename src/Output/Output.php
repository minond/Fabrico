<?php

namespace Fabrico\Output;

/**
 * defines how output content should be handled
 */
interface Output
{
    /**
     * handles output data
     * @return void
     */
    public function output();
}
