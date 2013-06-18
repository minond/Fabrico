<?php

namespace Fabrico\Test\Core;

use Fabrico\Test\Test;
use Fabrico\Core\Application;
use Fabrico\Core\Ext;
use Fabrico\Core\ExtensionManager;
use Fabrico\Test\Mock\Project\NoWriteConfiguration;
use Efficio\Cache\RuntimeCache;

class ExtensionManagerTest extends Test
{
    public $cache;
    public $conf;
    public $ext;

    public function setUp()
    {
        Ext::hardreset();
        $this->cache = new RuntimeCache;
        $this->conf = new NoWriteConfiguration($this->cache);
        $this->ext = new ExtensionManager($this->conf);
    }

    public function testEnabledFlagCanBeChecked()
    {
        $this->cache->set('ext', [ 'enabled' => ['ext1', 'ext2'] ]);
        $this->assertTrue($this->ext->enabled('ext1'));
        $this->assertTrue($this->ext->enabled('ext2'));
        $this->assertFalse($this->ext->enabled('ext3'));
    }

    public function testEnabledFlagCanBeCheckedUsingHelperClass()
    {
        $app = new Application;
        $app->setConfiguration($this->conf);

        $this->cache->set('ext', [ 'enabled' => ['ext1', 'ext2'] ]);
        $this->assertTrue(Ext::enabled('ext1'));
        $this->assertTrue(Ext::enabled('ext2'));
        $this->assertFalse(Ext::enabled('ext3'));
    }

    public function testExtensionsCanBeEnabled()
    {
        $this->cache->set('ext', [ 'enabled' => ['ext1', 'ext2'] ]);
        $this->ext->enable('ext3');
        $this->assertTrue($this->ext->enabled('ext1'));
        $this->assertTrue($this->ext->enabled('ext2'));
        $this->assertTrue($this->ext->enabled('ext3'));
        $this->assertFalse($this->ext->enabled('ext4'));
    }

    public function testExtensionsCanBeDisabled()
    {
        $this->cache->set('ext', [ 'enabled' => ['ext1', 'ext2'] ]);
        $this->ext->disable('ext2');
        $this->assertTrue($this->ext->enabled('ext1'));
        $this->assertFalse($this->ext->enabled('ext2'));
        $this->assertFalse($this->ext->enabled('ext4'));
    }

    public function testConfigurationValuesCanBeRetrieved()
    {
        $this->cache->set('ext/test', [ 'value' => 1 ]);
        $this->assertEquals(1, $this->ext->config('test:value'));
    }

    public function testConfigurationValuesCanBeRetrievedUsingHelperClass()
    {
        $app = new Application;
        $app->setConfiguration($this->conf);

        $this->cache->set('ext/test', [ 'value' => 1 ]);
        $this->assertEquals(1, Ext::config('test:value'));
    }

    /**
     * @expectedException Exception
     */
    public function testGettingInvalidConfigurationPathsThrowErrors()
    {
        $this->ext->config('test:value:invalid');
    }

    public function testConfigurationValuesCanBeUpdated()
    {
        $this->cache->set('ext/test', [ 'value' => 1 ]);
        $this->ext->config('test:value', 2);
        $this->assertEquals(2, $this->ext->config('test:value'));
    }

    /**
     * @expectedException Exception
     */
    public function testSettingInvalidConfigurationPathsThrowErrors()
    {
        $this->ext->config('test:value:invalid', 1);
    }
}
