<?php

namespace Fabrico;

use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\RuleBook;

trait EnvironmentInjenction
{
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

    protected function getResponse()
    {
        static $res;

        if (!$res) {
            $res = new Response;
            $res->setStatusCode(Status::NOT_FOUND);
        }

        return $res;
    }

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

