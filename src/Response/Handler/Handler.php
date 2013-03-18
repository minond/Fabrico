<?php

namespace Fabrico\Response\Handler;

use Fabrico\Core\Application;
use Fabrico\Request\Request;
use Fabrico\Response\Response;

/**
 * checks a request object and helps poulate a response
 */
abstract class Handler
{
    /**
     * handler levels
     */
    const OFF = 0;
    const NONE = 1;
    const HIGH = 2;
    const MEDIUM = 3;
    const LOW = 4;

    /**
     * importance level of handler
     * @var double
     */
    protected static $level = 0;

    /**
     * reference to current application
     * @var Application
     */
    protected $app;

    /**
     * should check a Request object and return true if this handler knows
     * how to generate a response.
     * @param Request $req
     * @return boolean
     */
    abstract public function canHandle(Request & $req);

    /**
     * takes a Request object and generates a response
     * @param Request $req
     * @param Response $res
     * @throws \Exception
     * @return boolean
     */
    abstract public function handle(Request & $req, Response & $res);

    /**
     * app setter
     * @param Application $app
     */
    final public function setApplication(Application & $app)
    {
        $this->app = $app;
    }

    /**
     * level getter
     * @return double
     */
    final public static function getLevel()
    {
        return static::$level;
    }
}
