<?php

namespace Fabrico;

use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Configurare\Configuration;

class Controller
{
    /**
     * view already rendered flag
     * @var boolean
     */
    protected $view_rendered = false;

    /**
     * @var Request
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;

    /**
     * @param Configuration
     */
    protected $conf;

    /**
     * @param Request $req
     * @param Response $res
     */
    final public function __construct(Request $req, Response $res)
    {
        $this->req = $req;
        $this->res = $res;
    }

    /**
     * handles an incoming request
     */
    public function handleRequest()
    {
    }

    /**
     * renders a view
     * @param string $path
     * @param array $data
     */
    protected function renderView($path, $data)
    {
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        if (!$this->conf) {
            $this->conf = $conf = new Configuration;
            $conf->setCache(new RuntimeCache);
            $conf->setFormat(Configuration::YAML);
            $conf->setDirectory(Conventions::DIR_CONFIG);

            if (file_exists(Conventions::INIT_CONFIG_FILE)) {
                call_user_func(function() use (& $conf) {
                    require_once Conventions::INIT_CONFIG_FILE;
                });
            }
        }

        return $this->conf;
    }

    /**
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration $conf)
    {
        $this->conf = $conf;
    }
}

