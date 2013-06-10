<?php

namespace Fabrico\Test\Mock\Cache;

use Fabrico\Cache\FileCache;

class NoIsResourceCache extends FileCache
{
    protected static $is_resource = 'Fabrico\Test\Mock\Cache\NoIsResourceCacheReturnFalse';
}

function NoIsResourceCacheReturnFalse()
{
    return false;
}
