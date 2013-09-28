<?php

namespace Fabrico;

use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\Rule;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;

trait EnvironmentInjenction
{
    /**
     * @param Configuration
     */
    protected $conf;

    /**
     * @param Response
     */
    protected $res;

    /**
     * @param Request
     */
    protected $req;

    /**
     * @return Configuration
     */
    protected function getConfiguration()
    {
        if (!$this->conf) {
            $this->conf = $conf = new Configuration;
            $conf->setCache(new RuntimeCache);
            $conf->setFormat(Configuration::YAML);
            $conf->setDirectory('config');

            if (file_exists('init/config.php')) {
                require_once 'init/config.php';
            }
        }

        return $this->conf;
    }

    /**
     * @return Response
     */
    protected function getResponse()
    {
        if (!$this->res) {
            $this->res = $res = new Response;
            $res->setStatusCode(Status::NOT_FOUND);
        }

        return $this->res;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        if (!$this->req) {
            $this->req = $req = new Request(true);
            $req->setUri($_SERVER['REDIRECT_URI']);
        }

        return $this->req;
    }
}

