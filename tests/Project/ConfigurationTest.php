<?php

namespace Fabrico\Test\Controller;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Project\ConfigurationFinder;
use Fabrico\Core\Application;

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
        $this->conf = new ConfigurationFinder;
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
        $this->assertTrue(is_array($cache));
        $this->assertTrue(array_key_exists('test_configuration', $cache));
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

    public function testInvalidConfigurationPathsReturnNull()
    {
        $this->assertNull($this->conf->get('test_configuration:one:does_not_exist'));
    }
}
