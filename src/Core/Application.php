<?php

namespace Fabrico\Core;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\Controller\Controller;
use Fabrico\Project\Configuration;

/**
 * base Fabrico application class. stores the request and reposnse object along
 * with basic information about the active project.
 */
class Application
{
    /**
     * holds all instances of Application objects created
     * @var Application[]
     */
    private static $cache = [];

    /**
     * holds the last Application created
     * @var Application
     */
    private static $last;

    /**
     * project root directory
     * @var string
     */
    private $root;

    /**
     * project root namespace
     * @var string
     */
    private $namespace;

    /**
     * current request
     * @var Request
     */
    private $request;

    /**
     * response we're sending back
     * @var Response
     */
    private $response;

    /**
     * project configuration file loader/manager
     * @var Configuration
     */
    private $configuration;

    /**
     * retrieve an Application
     * @param string $name
     * @return Application
     */
    public static function getInstance($name = null)
    {
        if (!$name) {
            return self::$last;
        } else {
            return array_key_exists($name, self::$cache) ?
                self::$cache[ $name ] : null;
        }
    }

    /**
     * caches the new Application
     * @param string $name - optional
     */
    public function __construct($name = null)
    {
        self::$last = $this;

        if ($name) {
            self::$cache[ $name ] = $this;
        }
    }

    /**
     * project root setter
     * @param string $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * project root setter
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * project namespace setter
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * project namespace setter
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * request setter
     * @param Request $req
     */
    public function setRequest(Request & $req)
    {
        $this->request = $req;
    }

    /**
     * request getter
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * response setter
     * @param Response $res
     */
    public function setResponse(Response & $res)
    {
        $this->response = $res;
    }

    /**
     * response getter
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * configuration setter
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration & $conf)
    {
        $this->configuration = $conf;
    }

    /**
     * configuration getter
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
