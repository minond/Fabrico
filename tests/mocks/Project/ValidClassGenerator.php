<?php

namespace Fabrico\Test\Mock\Project;

require_once 'InvalidClassGenerator.php';

class ValidClassGenerator extends InvalidClassGenerator
{
    public static $namespace = 'Testing';
}
