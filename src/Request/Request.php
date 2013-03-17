<?php

namespace Fabrico\Request;

/**
 * base interface for all incoming request (ie. Http, Cli)
 */
interface Request
{
    /**
     * @return boolean
     */
    public function valid();

    /**
     * data setter
     * @param array $data
     */
    public function setData(array & $data);

    /**
     * data getter
     * @return array
     */
    public function getData();

    /**
     * checks current state of request and returns a Response object that best
     * fits the requested data
     * @return Response
     */
    public function respondWith();
}
