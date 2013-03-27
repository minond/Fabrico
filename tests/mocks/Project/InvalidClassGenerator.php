<?php

namespace Fabrico\Test\Mock\Project;

use Fabrico\Project\ClassGenerator;

class InvalidClassGenerator
{
    use ClassGenerator;

    public function callGenerateFullClassNamespacePath($classname)
    {
        return self::generateFullClassNamespacePath($classname);
    }

    public function callHasProjectClass($classname)
    {
        return self::hasProjectClass($classname);
    }
}
