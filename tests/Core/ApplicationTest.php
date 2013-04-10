<?php

namespace Fabrico\Test\Core;

use Fabrico\Core\Application;
use Fabrico\Response\Http\Response;
use Fabrico\Request\Http\Request;
use Fabrico\Project\Configuration;
use Fabrico\Cache\RuntimeCache;
use Fabrico\Test\Test;

class ApplicationTest extends Test
{
    public $app;

    public function setUp()
    {
        $this->app = new Application;
    }

    public function testRootCanBeSetAndRetrieved()
    {
        $this->app->setRoot('/');
        $this->assertEquals('/', $this->app->getRoot());
    }

    public function testRequestCanBeSetAndRetrieved()
    {
        $req = new Request;
        $this->app->setRequest($req);
        $this->assertEquals($req, $this->app->getRequest());
    }

    public function testResponseCanBeSetAndRetrieved()
    {
        $res = new Response;
        $this->app->setResponse($res);
        $this->assertEquals($res, $this->app->getResponse());
    }

    public function testConfigurationCanBeSetAndRetrieved()
    {
        $conf = new Configuration(new RuntimeCache);
        $this->app->setConfiguration($conf);
        $this->assertEquals($conf, $this->app->getConfiguration());
    }

    public function testNamespaceCanBeSetAndRetrieved()
    {
        $res = new Response;
        $this->app->setNamespace('hi');
        $this->assertEquals('hi', $this->app->getNamespace());
    }

    public function testLastIntanceIsReturnedByDefault()
    {
        $app1 = new Application;
        $app2 = new Application;
        $this->assertTrue($app2 === Application::getInstance());
    }

    public function testIntanceCanBeRetrievedByName()
    {
        $app1 = new Application('one');
        $app2 = new Application('two');
        $this->assertTrue($app1 === Application::getInstance('one'));
    }
}
