<?php

namespace Fabrico\Response\Handler\Http;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\View\View;
use Fabrico\Output\Http\HtmlOutput;
use Fabrico\Response\Handler\Handler;

/**
 * outputs a view file
 */
class ViewFileHandler extends Handler
{
    /**
     * @inheritdoc
     */
    protected static $level = self::HIGH;

    /**
     * @var View
     */
    private $view;

    /**
     * view setter. requires view with a file set
     * @param View $view
     * @throws \InvalidArgumentException
     */
    public function setView(View $view)
    {
        if (!$view->getFile()) {
            throw new \InvalidArgumentException('Empty view given');
        }

        $this->view = $view;
    }

    /**
     * view getter
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * checks if the controller has the requested method
     * @return true
     */
    public function canHandle(Request & $req)
    {
        $ok = false;
        $view = null;

        if ($req->_uri || $this->view) {
            $view = $this->view ?: new View($req->_uri);
            $ok = $view->exists();

            if ($ok) {
                $this->view = $view;
            }
        }

        return $ok;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $out = new HtmlOutput;
        $out->setContent($this->view->render());
        $res = $this->app->getResponse()->setOutput($out);
    }
}
