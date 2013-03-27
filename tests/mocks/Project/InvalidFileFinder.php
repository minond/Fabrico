<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\FileFinder;

class InvalidFileFinder
{
    use FileFinder;

    public function callGenerateFileFilderFilePath($filename)
    {
        return self::generateFileFilderFilePath($filename);
    }

    public function callHasProjectFile($filename)
    {
        return self::hasProjectFile($filename);
    }

    public function callLoadProjectFile($filename)
    {
        return self::loadProjectFile($filename);
    }
}
