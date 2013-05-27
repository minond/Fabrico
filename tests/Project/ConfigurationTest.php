<?php

namespace Fabrico\Test\Controller;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Project\ConfigurationFinder;
use Fabrico\Core\Application;
use Fabrico\Cache\RuntimeCache;
use Fabrico\Cache\ReadOnceCache;

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

    public function testConfigurationValuesCanBeUpdated()
    {
        $orig = $this->conf->get('test_updates:value');
        $val = mt_rand();

        $this->assertTrue(
            $this->conf->set('test_updates:value', $val));
        $this->assertEquals($val,
            $this->conf->get('test_updates:value'));

        $this->conf->set('test_updates:value', $orig);
    }

    public function testNestedConfigurationValuesCanBeUpdated()
    {
        $orig = $this->conf->get('test_updates:values:value');
        $val = mt_rand();

        $this->assertTrue(
            $this->conf->set('test_updates:values:value', $val));
        $this->assertEquals($val,
            $this->conf->get('test_updates:values:value'));

        $this->conf->set('test_updates:values:value', $orig);
    }

    /**
     * @expectedException Exception
     */
    public function testSettingsInvalidConfigurationPathsThrowExceptions()
    {
        $this->assertNull($this->conf->set('test_updates:values:does_not_exist', 1));
    }

    /**
     * @expectedException Exception
     */
    public function testGettingInvalidConfigurationPathsThrowExceptions()
    {
        $this->assertNull($this->conf->get('test_configuration:one:does_not_exist'));
    }

    public function testConstantsAreMergedIntoConfigurationStrings()
    {
        $this->assertEquals('1 s 2 1', $this->conf->prepareRawConfigurationString(
            '%E_ERROR s %E_WARNING %E_ERROR'
        ));
    }

    public function testCacheCanBeReset()
    {
        $cache = new ReadOnceCache;
        $this->conf->setCache($cache);
        $this->assertEquals($cache, $this->conf->getCache());
    }
}
