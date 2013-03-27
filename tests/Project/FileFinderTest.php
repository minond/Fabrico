<?php

namespace Fabrico\Test\Output;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Project\InvalidFileFinder;
use Fabrico\Test\Mock\Project\ValidFileFinder;
use Fabrico\Core\Application;

require_once 'tests/mocks/Project/InvalidFileFinder.php';
require_once 'tests/mocks/Project/ValidFileFinder.php';

class FileFinderTest extends Test
{
    /**
     * @expectedException Exception
     */
    public function testADirectoryAndExtensionAreRequired()
    {
        $invalid = new InvalidFileFinder;
        $invalid->callGenerateFileFilderFilePath('name');
    }

    public function testApplicationRootIsUsedToGenerateFilePaths()
    {
        $dir = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dir);
        $dir = implode(DIRECTORY_SEPARATOR, $dir);

        $app = new Application;
        $app->setRoot($dir);

        $parts = explode(DIRECTORY_SEPARATOR, __FILE__);
        list($file) = explode('.', array_pop($parts));
        $valid = new ValidFileFinder;
        $this->assertEquals(__FILE__, $valid->callGenerateFileFilderFilePath($file));
    }

    public function testFilesCanBeFound()
    {
        $dir = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dir);
        $dir = implode(DIRECTORY_SEPARATOR, $dir);

        $app = new Application;
        $app->setRoot($dir);

        $parts = explode(DIRECTORY_SEPARATOR, __FILE__);
        list($file) = explode('.', array_pop($parts));
        $valid = new ValidFileFinder;
        $this->assertTrue($valid->callHasProjectFile($file));
    }

    public function testFilesCanBeLoaded()
    {
        $dir = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dir);
        $dir = implode(DIRECTORY_SEPARATOR, $dir);

        $app = new Application;
        $app->setRoot($dir);

        $parts = explode(DIRECTORY_SEPARATOR, __FILE__);
        list($file) = explode('.', array_pop($parts));
        $valid = new ValidFileFinder;
        $this->assertTrue($valid->callLoadProjectFile($file));
    }

    public function testInvalidFilesAreNotFound()
    {
        $dir = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dir);
        $dir = implode(DIRECTORY_SEPARATOR, $dir);

        $app = new Application;
        $app->setRoot($dir);

        $parts = explode(DIRECTORY_SEPARATOR, __FILE__);
        list($file) = explode('.', array_pop($parts));
        $valid = new ValidFileFinder;
        $this->assertFalse($valid->callHasProjectFile($file . 'fake'));
    }

    public function testInvalidFilesAreNotLoaded()
    {
        $dir = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($dir);
        $dir = implode(DIRECTORY_SEPARATOR, $dir);

        $app = new Application;
        $app->setRoot($dir);

        $parts = explode(DIRECTORY_SEPARATOR, __FILE__);
        list($file) = explode('.', array_pop($parts));
        $valid = new ValidFileFinder;
        $this->assertFalse($valid->callLoadProjectFile($file . 'fake'));
    }
}
