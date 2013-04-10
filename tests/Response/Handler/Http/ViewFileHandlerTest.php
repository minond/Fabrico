<?php

namespace Fabrico\Test\Response\Handler\Http;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\View\HandlesAllView;
use Fabrico\Response\Handler\Http\ViewFileHandler;
use Fabrico\Request\Http\Request;
use Fabrico\Response\Http\Response;
use Fabrico\Core\Application;

require_once 'tests/mocks/View/HandlesAllView.php';

class ViewFileHandlerTest extends Test
{
    /**
     * @var ViewFileHandler
     */
    public $handler;

    /**
     * @var View
     */
    public $view;

    /**
     * @var Request
     */
    public $request;

    public function setUp()
    {
        $this->handler = new ViewFileHandler;
        $this->view = new HandlesAllView;
        $this->request = new Request;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testViewsWithNoFilesAreNotAccepted()
    {
        $this->handler->setView($this->view);
    }

    public function testViewsWithFilesAreValid()
    {
        $this->view->setFile('file_name');
        $this->handler->setView($this->view);
        $this->assertEquals($this->view, $this->handler->getView());
    }

    public function testRequestUriIsNeeded()
    {
        $this->assertFalse($this->handler->canHandle($this->request));
    }

    public function testRequestUriCanBeUsed()
    {
        $data = ['_uri' => 'hi'];
        $this->request->setData($data);
        $this->assertFalse($this->handler->canHandle($this->request));
    }

    public function testViewsCanBeUsed()
    {
        $this->view->setFile('hi');
        $this->handler->setView($this->view);
        $this->assertTrue($this->handler->canHandle($this->request));
    }

    public function testViewsAreRendered()
    {
        $app = new Application;
        $response = new Response;
        $content = 'hi hi hi';

        $this->view->setFile('s');
        $this->view->setContent($content);
        $this->handler->setApplication($app);
        $this->handler->setView($this->view);
        $app->setResponse($response);

        $this->handler->handle();
        $output = $app->getResponse()->getOutput()->getContent();
        $this->assertEquals($content, $output);
    }
}
