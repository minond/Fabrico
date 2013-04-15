<?php

namespace Fabrico\Response\Handler\Http;

use Fabrico\Request\Request;
use Fabrico\View\View;
use Fabrico\Output\Http\HtmlOutput;
use Fabrico\Response\Handler\Handler;
use Fabrico\Project\Configurable;

/**
 */
class InvalidRequestHandler extends Handler
{
    use Configurable;

    /**
     * @inheritdoc
     */
    protected static $level = self::LOW;

    /**
     * @see Fabrico\Project\Configurable
     */
    protected static $confpath = 'project:handlers:config';

    /**
     * 404 file
     * @var string
     */
    protected $file;

    /**
     * checks if the controller has the requested method
     * @return true
     */
    public function canHandle(Request & $req)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $view = new View;
        $out = new HtmlOutput;
        $file = $this->getPropertyValue('file');

        $view->setFile($file);
        $out->setContent($view->render());

        $this->app->getResponse()->setOutput($out);
    }
}
