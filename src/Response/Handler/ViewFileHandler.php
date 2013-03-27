<?php

namespace Fabrico\Response\Handler;

use Fabrico\Request\Request;
use Fabrico\Response\Response;
use Fabrico\View\View;
use Fabrico\Output\HtmlOutput;

/**
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
     * checks if the controller has the requested method
     * @return true
     */
    public function canHandle(Request & $req)
    {
        $ok = false;
        $view = null;

        if ($req->_uri) {
            $view = new View($req->_uri);
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
        $data = new \StdClass;
        $data->name = 'dddd';
        $out->setContent($this->view->render([ 'name' => 'Marcos Minond' ], $data));
        $res = $this->app->getResponse()->setOutput($out);
    }
}
