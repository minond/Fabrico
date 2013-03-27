<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\FileFinder;

class ValidFileFinder extends InvalidFileFinder
{
    public static $dir = '/';
    public static $ext = '.php';
}
