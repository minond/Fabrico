<?php

namespace Fabrico\Output;

/**
 * defined getHeaders which is used by HttpResponse
 */
interface HttpOutput
{
    /**
     * returns an array of default headers for output type
     * @return array
     */
    public function getHeaders();
}
