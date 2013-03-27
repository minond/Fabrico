<?php

namespace Fabrico\Request;

use Fabrico\Core\Application;
use Fabrico\Response\Response;
use Fabrico\Response\Handler\Handler;

/**
 * base interface for all incoming request (ie. Http, Cli)
 */
abstract class Request
{
    /**
     * available response handlers
     * @var Handler[]
     */
    private $handlers = [];

    /**
     * handler for request
     * @var Handler
     */
    private $handler;

    /**
     * request parameters
     * @var array
     */
    protected $data;

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
     * handler adder
     * @param string $handler
     */
    final public function addResponseHandler($handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * handler getter
     * @return Handler
     */
    final public function getHandler()
    {
        return $this->handler;
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
     * @return boolean
     */
    public function valid()
    {
        return $this->hasHandler();
    }

    /**
     * @see getResponseHandler
     * @param Application $app
     * @return Handler
     */
    public function prepareHandler(Application $app)
    {
        return $this->handler = $this->getResponseHandler($app);
    }

    /**
     * checks if the handler for this request has already been set
     * @return boolean
     */
    protected function hasHandler()
    {
        return !!$this->handler && $this->handler instanceof Handler;
    }

    /**
     * returns the best possible response handler
     * @param Application $app
     * @return Handler
     */
    private function getResponseHandler(Application $app)
    {
        $list = $this->groupHandlersList();
        $handler = $this->findBestHandler($app, $list);

        if ($handler) {
            $handler->setApplication($app);
        }

        return $handler;
    }

    /**
     * returns a list of available handlers grouped by their level
     * @return array
     */
    private function groupHandlersList()
    {
        $list = [];

        foreach ($this->handlers as $handler) {
            $level = $handler::getLevel();

            if (!isset($list[ $level ])) {
                $list[ $level ] = [];
            }

            $list[ $level ][] = $handler;
        }

        return $list;
    }

    /**
     * takes a list of handler names, and returns the first one that can handle
     * this current request
     * @param Application $app
     * @param array $list
     * @return Handler
     */
    private function findBestHandler(Application $app, array $list)
    {
        foreach ($list as $level) {
            foreach ($level as $handler) {
                $obj = new $handler;

                if ($obj->canHandle($this)) {
                    return $obj;
                }
            }
        }

        return false;
    }
}
