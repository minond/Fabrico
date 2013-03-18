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
     * data setter
     * @param array $data
     */
    abstract public function setData(array & $data);

    /**
     * data getter
     * @return array
     */
    abstract public function getData();

    /**
     * checks current state of request and returns a Response object that best
     * fits the requested data
     * @param Application $app
     * @return Response
     */
    abstract public function generateResponse(Application $app);

    /**
     * handler adder
     * @param string $handler
     */
    final public function addResponseHandler($handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * triggers Handler::handle
     * @param Response $res
     */
    final public function handle(Response $res)
    {
        $this->handler->handle($this, $res);
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
     */
    protected function prepareHandler(Application $app)
    {
        $this->handler = $this->getResponseHandler($app);
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
        $handler = $this->findBestHandler($list);
        $handler->setApplication($app);
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
     * @param array $list
     * @throws \Exception
     * @return Handler
     */
    private function findBestHandler(array $list)
    {
        foreach ($list as $level) {
            foreach ($level as $handler) {
                $obj = new $handler;

                if ($obj->canHandle($this)) {
                    return $obj;
                }
            }
        }

        throw new \Exception('No handler found');
    }
}
