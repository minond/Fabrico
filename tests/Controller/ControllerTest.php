<?php

namespace Fabrico\Test\Controller;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Controller\ControllerFinder;
use Fabrico\Core\Application;
use Fabrico\Controller\Controller;

class ControllerTest extends Test
{
    public function testControllersCanBeFoundAndLoaded()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);
        $app->setNamespace('Fabrico');

        ControllerFinder::setDir('tests/mocks/Controller');
        ControllerFinder::setNs('Test\Mock\Controller');

        $con = ControllerFinder::load('ControllerFinder');
        $this->assertTrue($con instanceof ControllerFinder);
    }

    public function testControllersCanBeFoundAndLoadedWithACaseInsensitiveSearch()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);
        $app->setNamespace('Fabrico');

        ControllerFinder::setDir('tests/mocks/Controller');
        ControllerFinder::setNs('Test\Mock\Controller');

        $con = ControllerFinder::load('controllerfinder');
        $this->assertTrue($con instanceof ControllerFinder);
    }

    public function testInvalidControllersAreNotLoaded()
    {
        $app = new Application;
        $app->setRoot('/');
        $app->setNamespace('Test');
        $this->assertNull(Controller::load('fake'));
    }
}
