<?php

namespace Fabrico\Output\Http;

/**
 * defined getHeaders which is used by HttpResponse
 */
interface Output
{
    /**
     * returns an array of default headers for output type
     * @return array
     */
    public function getHeaders();
}
