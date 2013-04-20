<?php

namespace Fabrico\Test\Response\Handler;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Response\Handler\Http\PublicConfigurationHandler;
use Fabrico\Test\Mock\View\HandlesAllView;
use Fabrico\Core\Application;
use Fabrico\Request\Http\Request;
use Fabrico\Response\Http\Response;

require_once 'tests/mocks/Response/Handler/Http/PulicConfigurationHandler.php';
require_once 'tests/mocks/View/HandlesAllView.php';

class ViewFileHandlerTest extends Test
{
    /**
     * @var PublicConfigurationHandler
     */
    public $handler;

    public function setUp()
    {
        $this->handler = new PublicConfigurationHandler;
    }

    public function testHandlerCanHandleAnything()
    {
        $req = new Request;
        $this->assertTrue($this->handler->canHandle($req));
    }

    public function testHandlerChecksConfigurationFor404File()
    {
        $res = new Response;
        $app = new Application;
        $app->setResponse($res);

        $view = new HandlesAllView;
        $view->setContent('hi');

        $this->handler->setPropertyValue('file', 'ignore');
        $this->handler->setView($view);
        $this->handler->setApplication($app);
        $this->handler->handle();

        $view2 = $this->handler->getView();
        $this->assertEquals('ignore', $view2->getFile());
        $this->assertEquals('hi', $res->getOutput()->getContent());
    }
}
