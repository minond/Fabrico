<?php

namespace Fabrico\Test\Output;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Project\InvalidClassGenerator;
use Fabrico\Test\Mock\Project\ValidClassGenerator;
use Fabrico\Core\Application;

require_once 'tests/mocks/Project/InvalidClassGenerator.php';
require_once 'tests/mocks/Project/ValidClassGenerator.php';

class ClassGeneratorTest extends Test
{
    /**
     * @expectedException Exception
     */
    public function testNamespacesAreRequiredToGenerateClassNames()
    {
        $invalid = new InvalidClassGenerator;
        $invalid->callGenerateFullClassNamespacePath('hi');
    }

    public function testApplicationNamespaceBaseIsUsedToGenerateFullNamespace()
    {
        $app = new Application;
        $app->setNamespace('MyApplication\Hi');
        $valid = new ValidClassGenerator;
        $this->assertEquals(
            '\MyApplication\Hi\Testing\ClassName',
            $valid->callGenerateFullClassNamespacePath('ClassName')
        );
    }

    public function testClassesAreFound()
    {
        $app = new Application;
        $app->setNamespace('Fabrico\Test');
        ValidClassGenerator::$namespace = 'Output';
        $valid = new ValidClassGenerator;
        $this->assertTrue($valid->callHasProjectClass('ClassGeneratorTest'));
    }
}
