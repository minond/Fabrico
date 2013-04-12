<?php

namespace Fabrico\Test\Cache;

use Fabrico\Cache\RuntimeCache;
use Fabrico\Test\Test;

class CacheTest extends Test
{
    /**
     * @var RuntimeCache
     */
    public $cache;

    public function setUp()
    {
        $this->cache = new RuntimeCache;
    }

    public function testValuesCanBeSetUsingObjectNotation()
    {
        $val = 'val';
        $this->assertEquals($val, $this->cache->key = $val);
    }

    public function testValuesCanBeRetrievedUsingObjectNotation()
    {
        $val = 'val';
        $this->cache->key = $val;
        $this->assertEquals($val, $this->cache->key);
    }

    public function testIssetWorksUsingObjectNotation()
    {
        $this->assertFalse(isset($this->cache->key));
        $this->cache->key = 'val';
        $this->assertTrue(isset($this->cache->key));
    }

    public function testUnsetWorksUsingObjectNotation()
    {
        $this->cache->key = 'val';
        unset($this->cache->key);
        $this->assertFalse(isset($this->cache->key));
    }

    public function testValuesCanBeSetUsingArrayNotation()
    {
        $val = 'val';
        $this->assertEquals($val, $this->cache['key'] = $val);
    }

    public function testValuesCanBeRetrievedUsingArrayNotation()
    {
        $val = 'val';
        $this->cache['key'] = $val;
        $this->assertEquals($val, $this->cache['key']);
    }

    public function testIssetWorksUsingArrayNotation()
    {
        $this->assertFalse(isset($this->cache['key']));
        $this->cache['key'] = 'val';
        $this->assertTrue(isset($this->cache['key']));
    }

    public function testUnsetWorksUsingArrayNotation()
    {
        $this->cache['key'] = 'val';
        unset($this->cache['key']);
        $this->assertFalse(isset($this->cache['key']));
    }
}
