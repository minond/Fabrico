<?php

namespace Fabrico\Test\Cache;

use Fabrico\Cache\ReadOnceCache;
use Fabrico\Test\Test;

class ReadOnceCacheTest extends Test
{
    /**
     * @var RuntimeCache
     */
    public $cache;

    public function setUp()
    {
        $this->cache = new ReadOnceCache;
    }

    public function testValuesCanBeSet()
    {
        $this->assertTrue($this->cache->set('name', 'Marcos'));
        $this->assertTrue($this->cache->has('name'));
    }

    public function testValuesCanBeSetThenRead()
    {
        $this->cache->set('name', 'Marcos');
        $this->assertEquals('Marcos', $this->cache->get('name'));
    }

    public function testValuesCanBeSetThenReadAndIsThenUnset()
    {
        $this->cache->set('name', 'Marcos');
        $this->cache->get('name');
        $this->assertFalse($this->cache->has('name'));
    }
}
