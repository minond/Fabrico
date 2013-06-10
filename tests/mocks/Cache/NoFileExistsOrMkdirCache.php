<?php

namespace Fabrico\Test\Mock\Cache;

use Fabrico\Cache\FileCache;

class NoFileExistsOrMkdirCache extends FileCache
{
    protected static $file_exists = 'Fabrico\Test\Mock\Cache\NoFileExistsOrMkdirCacheReturnFalse';
    protected static $mkdir = 'Fabrico\Test\Mock\Cache\NoFileExistsOrMkdirCacheReturnFalse';
}

function NoFileExistsOrMkdirCacheReturnFalse()
{
    return false;
}
