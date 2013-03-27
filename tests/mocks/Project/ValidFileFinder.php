<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\FileFinder;

class ValidFileFinder extends InvalidFileFinder
{
    public static $dir = 'Project';
    public static $ext = '.php';
}
