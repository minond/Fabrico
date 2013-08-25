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
     * @return Configuration
     */
    protected function getConfiguration()
    {
        static $conf;

        if (!$conf) {
            $conf = new Configuration;
            $conf->setCache(new RuntimeCache);
            $conf->setFormat(Configuration::YAML);
            $conf->setDirectory('configuration');
        }

        return $conf;
    }

    /**
     * @return Response
     */
    protected function getResponse()
    {
        static $res;

        if (!$res) {
            $res = new Response;
            $res->setStatusCode(Status::NOT_FOUND);
        }

        return $res;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        static $req;

        if (!$req) {
            $req = new Request(true);
            $req->setUri($_SERVER['REDIRECT_URI']);
        }

        return $req;
    }
}

