<?php

namespace Fabrico\Controller;

use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Dataset\Model;
use Efficio\Dataset\Collection;
use Efficio\Utilitatis\Word;

abstract class BaseController
{
    /**
     * standard actions
     */
    const INDEX_ACTION = 'index';

    /**
     * request's response
     * @var mixed
     */
    public $resource;

    /**
     * @var Request
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;

    /**
     * @param Request $req
     */
    public function setRequest(Request $req)
    {
        $this->req = $req;
    }

    /**
     * @param Response $res
     */
    public function setResponse(Response $res)
    {
        $this->res = $res;
    }

    /**
     * updates the response object's Location header
     * @param string $href
     */
    protected function redirectTo($href)
    {
        $this->res->header['Location'] = $href;
    }

    protected function resource($resource)
    {
        $key = 0;
        $base_class_name = function ($full_class) {
            $parts = explode('\\', $full_class);
            return array_pop($parts);
        };

        if ($resource instanceof Collection) {
            $word = new Word;
            $model = $base_class_name($resource->collectionOf());
            $key = strtolower($word->pluralize($model));
        } elseif ($resource instanceof Model) {
            $model = $base_class_name(get_class($resource));
            $key = strtolower($model);
        }

        $this->resource = [ $key => $resource ];
    }
}
