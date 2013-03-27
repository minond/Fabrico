<?php

namespace Fabrico\Test\Controller;

use Fabrico\Test\Test;
use Fabrico\Test\OvertClass;
use Fabrico\Test\Mock\Controller\EmptyController;
use Fabrico\Core\Application;
use Fabrico\Controller\Controller;

require_once 'tests/mocks/Controller/EmptyController.php';

class ControllerTest extends Test
{
    public function testControllersCanBeFoundAndLoaded()
    {
        // $controller = new OvertClass(new EmptyController);
        // $controller->__overwriteStaticFunction('loadProjectFile', true);
        // $controller->__overwriteStaticFunction('canFileProjectClass', true);
        // $controller->__overwriteStaticFunction(
        //     'generateFullClassNamespacePath',
        //     'Fabrico\Test\Mock\Controller\EmptyController'
        // );
        // $loaded_controller = $controller::load('ignore', $controller);
        // $this->assertTrue($loaded_controller instanceof EmptyController);
        $this->markTestIncomplete('Problem with static methods, ignoring code coverage');
    }

    public function testInvalidControllersAreNotLoaded()
    {
        $app = new Application;
        $app->setRoot('/');
        $app->setNamespace('Test');
        $this->assertNull(Controller::load('fake'));
    }
}
