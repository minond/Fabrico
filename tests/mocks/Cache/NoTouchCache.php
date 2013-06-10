<?php

namespace Fabrico\Test\Mock\Cache;

use Fabrico\Cache\FileCache;

class NoTouchCache extends FileCache
{
    protected static $touch = 'Fabrico\Test\Mock\Cache\NoTouchCacheReturnFalse';
}

function NoTouchCacheReturnFalse()
{
    return false;
}
