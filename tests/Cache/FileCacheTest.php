<?php

namespace Fabrico\Test\Cache;

use Fabrico\Cache\FileCache;
use Fabrico\Cache\RuntimeCache;
use Fabrico\Project\Configuration;
use Fabrico\Core\Application;
use Fabrico\Test\Test;
use Fabrico\Test\Mock\Cache\NoFileExistsOrMkdirCache;
use Fabrico\Test\Mock\Cache\NoIsResourceCache;
use Fabrico\Test\Mock\Cache\NoTouchCache;

class FileCacheTest extends Test
{
    /**
     * @var RuntimeCache
     */
    public $cache;

    /**
     * cache directory name
     * @var string
     */
    private $dirname;

    /**
     * cache file name
     * @var string
     */
    private $filename;

    public function setUp()
    {
        $cache = new RuntimeCache;
        $conf = new Configuration($cache);
        $app = new Application;
        $app->setConfiguration($conf);

        // so everything goes under /tmp/Fabrico/FabricoUnitTest/
        $cache->set('project', [ 'namespace' => 'FabricoUnitTest' ]);

        $this->cache = new FileCache(uniqid() . mt_rand(1, 10000) . 'testing.cache', true);
        $this->filename = $this->cache->getFileName();
        $this->dirname = dirname($this->filename);
    }

    public function tearDown()
    {
        if (!is_null($this->cache) && is_dir($this->dirname)) {
            `rm -rf {$this->dirname}/*`;

            if (!rmdir($this->dirname)) {
                throw new \Exception('Error deleting test cache file in: ' .
                    __METHOD__);
            }

            unset($this->cache);
        }
    }

    public function testTemporaryDirectoryIsCreated()
    {
        $this->assertTrue(is_dir($this->dirname));
    }

    public function testTemporaryFileIsCreated()
    {
        $this->assertTrue(is_file($this->filename));
    }

    /**
     * @expectedException Exception
     */
    public function testGeneratingANewCacheObjectWithAFileAlreadyInUseTriggersError()
    {
        new FileCache($this->filename);
    }

    public function testExistingCacheObjectsCanBeRetrievedUsingFileNames()
    {
        $this->assertEquals($this->cache, FileCache::create($this->filename));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Error creating cache file directory: /tmp/Fabrico/FabricoUnitTest
     */
    public function testCreatingDirectoryErrors()
    {
        $cache = new NoFileExistsOrMkdirCache(uniqid() . mt_rand(1, 10000) . 'testing.cache', true);
    }

    /**
     * @expectedException Exception
     */
    public function testCreatingFileErrors()
    {
        $cache = new NoTouchCache(uniqid() . mt_rand(1, 10000) . 'testing.cache', true);
    }

    /**
     * @expectedException Exception
     */
    public function testReadingFileErrors()
    {
        $cache = new NoIsResourceCache(uniqid() . mt_rand(1, 10000) . 'testing.cache', true);
    }

    public function testKeySetter()
    {
        $val = uniqid();
        $this->assertTrue($this->cache->set('name', $val));
        $this->assertTrue($this->cache->has('name'));
    }

    public function testKeyGetter()
    {
        $val = uniqid();
        $this->cache->set('name', $val);
        $this->assertEquals($val, $this->cache->get('name'));
    }

    public function testKeysCanBeDeleted()
    {
        $val = uniqid();
        $this->cache->set('name', $val);
        $this->assertTrue($this->cache->has('name'));
        $this->cache->del('name');
        $this->assertFalse($this->cache->has('name'));
    }

    public function testDataIsSavedInFile()
    {
        $str = '{"fname":"Marcos"}';
        $this->cache->set('fname', 'Marcos');
        $this->cache->__destruct();
        $this->assertEquals($str, trim(file_get_contents($this->filename)));
    }

    public function testDataIsReloadedFromExistsingCacheFiles()
    {
        $str = '{"fname":"Marcos"}';
        $this->cache->set('fname', 'Marcos');
        $this->cache->__destruct();

        $cache = new FileCache($this->filename);
        $this->assertEquals($cache->get('fname'), 'Marcos');
    }

    public function testCacheCanBeCleared()
    {
        $this->cache->set('name 1', 'Marcos');
        $this->cache->set('name 2', 'Marcos');
        $this->cache->set('name 3', 'Marcos');
        $this->assertEquals(3, count($this->cache));
        $this->cache->clear();
        $this->assertEquals(0, count($this->cache));
    }
}
