<?php

namespace Fabrico\Request;

use Fabrico\Response\HttpResponse;
use Fabrico\Output\TextOutput;
use Fabrico\Output\HtmlOutput;
use Fabrico\Output\JsonOutput;

/**
 * handles all http requests
 */
class HttpRequest implements Request
{
    /**
     * request parameters
     * @var array
     */
    private $data;

    /**
     * gives access to $data values
     * @param string $var
     * @return mixed
     */
    public function __get($var)
    {
        return array_key_exists($var, $this->data) ?
            $this->data[ $var ] : null;
    }

    /**
     * gives access to $data values
     * @param string $var
     * @param mixed $val
     */
    public function __set($var, $val)
    {
        return array_key_exists($var, $this->data) ?
            $this->data[ $var ] = $val : null;
    }

    /**
     * we'll require one of the following (in this order):
     * # a controller action
     * # a controller method
     * # a file
     * @return boolean
     */
    public function valid()
    {
        return true;
    }

    /**
     * data setter
     * @param array $data
     */
    public function setData(array & $data)
    {
        $this->data = & $data;
    }

    /**
     * data getter
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function respondWith()
    {
        $res = new HttpResponse;
        $out = new TextOutput;
        $res->setOutput($out);
        return $res;
    }
}
