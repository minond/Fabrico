<?php

namespace Fabrico\Test\View;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\View\AnyView;
use Fabrico\Core\Application;
use Fabrico\View\View;

require_once 'tests/mocks/View/AnyView.php';

class ViewTest extends Test
{
    /**
     * @var View
     */
    public $view;

    public function setUp()
    {
        $this->view = new View;
    }

    public function testFileCanBeSetFromConstructor()
    {
        $view = new View('file');
        $this->assertEquals('file', $view->getFile());
    }

    public function testFileCanBeSetFromSetter()
    {
        $this->view->setFile('sss');
        $this->assertEquals('sss', $this->view->getFile());
    }

    public function testViewFilesCanBeFound()
    {
        $fileinfo = pathinfo(__FILE__);
        $filename = $fileinfo['filename'];

        $dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_shift($dirs);
        $dir = implode(DIRECTORY_SEPARATOR, $dirs);

        $app = new Application;
        $app->setRoot(DIRECTORY_SEPARATOR);

        AnyView::setExt('.php');
        AnyView::setDir($dir);

        $view = new AnyView($filename);
        $this->assertTrue($view->exists(true));
    }

    public function testViewsCanBeRendered()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $view = new AnyView('withjusttext');
        $this->assertEquals('hi', trim($view->render()));
    }

    public function testViewsCanBeRenderedWithData()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $view = new AnyView('withdata');
        $this->assertEquals('hi hi hi', trim($view->render([
            'name' => 'hi'
        ])));
    }

    public function testViewsCanBeRenderedWithContext()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $context = new \StdClass;
        $context->name = 'hi';

        $view = new AnyView('withcontext');
        $this->assertEquals('hi hi hi', trim($view->render([], $context)));
    }

    public function testViewsCanBeRenderedWithDataAndContext()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $context = new \StdClass;
        $context->name = 'hi';

        $view = new AnyView('witheverything');
        $this->assertEquals('hi hi hi hi hi', trim($view->render([
            'name' => 'hi'
        ], $context)));
    }

    public function testViewsCanBeRenderedUsingStaticFunction()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $this->assertEquals('hi', trim(AnyView::generate('withjusttext')));
    }

    public function testViewsCanBeRenderedWithDataUsingStaticFunction()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $this->assertEquals('hi hi hi', trim(AnyView::generate('withdata', [
            'name' => 'hi'
        ])));
    }

    public function testViewsCanBeRenderedWithContextUsingStaticFunction()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $context = new \StdClass;
        $context->name = 'hi';

        $this->assertEquals('hi hi hi', trim(AnyView::generate('withcontext',
            [], $context)));
    }

    public function testViewsCanBeRenderedWithDataAndContextUsingStaticFunction()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        AnyView::setExt('.php');
        AnyView::setDir('tests/mocks/View/views');

        $context = new \StdClass;
        $context->name = 'hi';

        $this->assertEquals('hi hi hi hi hi', trim(AnyView::generate('witheverything', [
            'name' => 'hi'
        ], $context)));
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidViewFilesTriggerError()
    {
        View::generate('junkfile');
    }

    public function testFileExtensionAreFound()
    {
        $this->view->setFile('file.extension');
        $this->assertEquals('file', $this->view->getFile());
        $this->assertEquals('.extension', $this->view->getExtension());
    }
}
