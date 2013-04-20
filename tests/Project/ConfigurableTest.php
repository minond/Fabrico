<?php

namespace Fabrico\Test\Controller;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Project\Configurable;
use Fabrico\Test\Mock\Project\ConfigurationFinder;
use Fabrico\Core\Application;
use Fabrico\Cache\RuntimeCache;

require_once 'tests/mocks/Project/ConfigurationFinder.php';
require_once 'tests/mocks/Project/Configurable.php';

class ConfigurableTest extends Test
{
    /**
     * @var Configurable
     */
    public $conf;

    public function setUp()
    {
        ConfigurationFinder::setDir('tests/mocks/Project');

        $app = new Application;
        $app->setRoot(FABRICO_ROOT);
        $conf = new ConfigurationFinder(new RuntimeCache);
        $app->setConfiguration($conf);
        $this->conf = new Configurable;
    }

    /**
     * @expectedException Exception
     */
    public function testAConfigurationPathIsRequired()
    {
        $this->conf->setConfpath(false);
        $this->conf->triggerGetPropertyValue('hi');
    }

    public function testPropertyCanBeRetrievedFromConfiguration()
    {
        $this->conf->setConfpath('test_configuration');
        $val = $this->conf->triggerGetPropertyValue('prop');
        $this->assertEquals('hihihi', $val);
    }

    public function testPropertyCanBeRetrievedFromObject()
    {
        $this->conf->setConfpath('test_configuration');
        $this->conf->setVar1('testing');
        $val = $this->conf->triggerGetPropertyValue('var1');
        $this->assertEquals('testing', $val);
    }
}
