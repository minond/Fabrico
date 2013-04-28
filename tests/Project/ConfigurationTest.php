<?php

namespace Fabrico\Test\Controller;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Project\ConfigurationFinder;
use Fabrico\Core\Application;
use Fabrico\Cache\RuntimeCache;

require_once 'tests/mocks/Project/ConfigurationFinder.php';

class ConfigurationTest extends Test
{
    /**
     * @var Configuration
     */
    public $conf;

    public function setUp()
    {
        ConfigurationFinder::setDir('tests/mocks/Project');

        $app = new Application;
        $app->setRoot(FABRICO_ROOT);
        $this->conf = new ConfigurationFinder(new RuntimeCache);
    }

    public function testFilesCanBeFoundAndLoaded()
    {
        $data = $this->conf->load('test_configuration');
        $this->assertTrue($data['always_true']);
    }

    public function testInvalidFilesReturnNull()
    {
        $this->assertNull($this->conf->load('does_not_exist'));
    }

    public function testConfigurationObjectsStartOutWithAnEmptyCache()
    {
        $this->assertEquals(0, count($this->conf->getCache()));
    }

    public function testFilesAreCached()
    {
        $data = $this->conf->load('test_configuration');
        $cache = $this->conf->getCache();
        $this->assertTrue($cache->has('test_configuration'));
        $this->assertEquals($data, $cache['test_configuration']);
    }

    public function testConfigurationValuesCanBeRetrievedOneAtATime()
    {
        $this->assertTrue($this->conf->get('test_configuration:always_true'));
    }

    public function testNestedConfigurationValuesCanBeRetrieved()
    {
        $this->assertEquals('hi',
            $this->conf->get('test_configuration:one:two:three:four:five'));
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidConfigurationPathsThrowExceptions()
    {
        $this->assertNull($this->conf->get('test_configuration:one:does_not_exist'));
    }

    public function testConstantsAreMergedIntoConfigurationStrings()
    {
        $this->assertEquals('1 s 2 1', $this->conf->prepareRawConfigurationString(
            '%E_ERROR s %E_WARNING %E_ERROR'
        ));
    }
}
