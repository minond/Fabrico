<?php

namespace Fabrico;

use Closure;
use StdClass;
use Exception;
use RuntimeException;
use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\RuleBook;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;
use Efficio\Utilitatis\Word;
use Twig_Environment as TwigEnv;
use Twig_Loader_Filesystem as TwigFs;
use Fabrico\Controller\BaseController;
use Fabrico\Initializer\JitInitializer;
use Fabrico\Error\Renderer\NoViewsFoundException;

class Application
{
    /**
     * tracks which initializer files have already been loaded
     * @var string[]
     */
    protected $initialized = [];

    /**
     * @param Configuration
     */
    protected $conf;

    /**
     * @var RuleBook
     */
    protected $rules;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @param Application
     */
    private static $app;

    /**
     * bind the application that should be used by Application::call
     * @param Application $app
     */
    public static function bind(Application $app)
    {
        return self::$app = $app;
    }

    /**
     * bind a function the Application::$app and call it
     * @param Closure $action
     * @return mixed
     */
    public static function call(Closure $action)
    {
        $action = Closure::bind($action, self::$app, get_class(self::$app));
        return $action();
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->conf;
    }

    /**
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration $conf)
    {
        $this->conf = $conf;
    }

    /**
     * @return RuleBook
     */
    public function getRuleBook()
    {
        return $this->rules;
    }

    /**
     * @param RuleBook $rules
     */
    public function setRuleBook(RuleBook $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param Renderer $renderer
     */
    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * I hate php. need non-static for closure binding
     * load an initializer file
     * @param string $name
     * @param array $args, default: array
     */
    public function initialize($name, array $args = [])
    {
        $word = new Word;
        $init = false;

        $project_init = sprintf(
            '%s\Initializer\%s',
            $this->conf->get('app:namespace'),
            $word->classicalCase($name)
        );

        $standard_init = sprintf(
            'Fabrico\Initializer\StdInitializers\%s',
            $word->classicalCase($name)
        );

        if (!in_array($name, $this->initialized)) {
            $this->initialized[] = $name;

            if (class_exists($project_init)) {
                $init = $project_init;
            } elseif (class_exists($standard_init)) {
                $init = $standard_init;
            }

            if ($init) {
                $initializer = new $init;

                if ($initializer instanceof JitInitializer) {
                    $initializer->setConfiguration($this->conf);
                    $initializer->setProperties($args);
                    return $initializer->initialize();
                } else {
                    throw new RuntimeException(sprintf(''));
                }
            }
        }
    }

    /**
     * handle a request
     * @param Request $req
     * @param Response $res
     * @return boolean success
     */
    public function route(Request $req, Response $res)
    {
        $ok = false;
        $route = $this->rules->matching($req, true);

        if ($route) {
            // defaults
            $route = array_merge([
                'format' => 'html',

                // can be a static resource
                'base' => '',
                'file' => '',

                // or an action call
                'action' => '',
                'controller' => '',
                'namespace' => $this->conf->get('app:namespace'),
            ], $route);

            if ($route['file']) {
                // file request
                $ok = $this->runFileRequest($route, $req, $res);
            } else {
                // action request
                $ok = $this->runActionRequest($route, $req, $res);
            }
        }

        $this->setResponseContentType($res, $route['format']);
        return $ok;
    }

    /**
     * check a route's format property and updates the response's content type
     * @param Response & $res
     * @param string $format
     */
    private function setResponseContentType(Response & $res, $format)
    {
        switch ($format) {
            case 'txt':
                $res->setContentType(Response::TEXT);
                break;

            case 'json':
                $res->setContentType(Response::JSON);
                break;

            case 'html':
            default:
                $res->setContentType(Response::HTML);
                break;
        }
    }

    /**
     * get a static file
     * @param array $route
     * @param Request $req
     * @param Response $res
     * @return boolean, action found and called
     */
    private function runFileRequest(array $route, Request $req, Response $res)
    {
        $file = $route['file'];
        $base = $route['base'];
        $frmt = $route['format'];

        $path = $base . DIRECTORY_SEPARATOR . $file . ($frmt ? ".$frmt" : '');
        $ok = file_exists($path);

        if ($ok) {
            $res->setContent($this->renderer->render($this, $path));
        }

        return $ok;
    }

    /**
     * call a controller function
     * @param array $route
     * @param Request $req
     * @param Response $res
     * @return boolean, action found and called
     */
    private function runActionRequest(array $route, Request $req, Response $res)
    {
        $ok = false;

        // route info
        $format = $route['format'];
        $action = $route['action'];
        $namespace = $route['namespace'];
        $controller = $route['controller'];

        // views holder and controller name
        $views = sprintf('views/%s/', strtolower($controller));
        $controller = sprintf('%s\Controller\%s', $namespace, $controller);

        // valid controller and action?
        $ok = class_exists($controller) &&
            method_exists($controller, $action) &&
            is_callable([ $controller, $action ]);

        if ($ok) {
            $controller = new $controller;

            if ($controller instanceof BaseController) {
                $controller->setRequest($req);
                $controller->setResponse($res);
            }

            $viewdata = $controller->{ $action }($req, $res) ?:
                $controller->resource;

            if (!$res->getStatusCode()) {
                $res->setStatusCode(Status::OK);
            }

            // content already set: getConfiguration returns something
            // redirect: Location header is set
            if (!$res->getContent() && !isset($res->header['Location'])) {
                $res->setContent($this->renderer->render(
                    $this,
                    sprintf('%s%s.%s', $views, $action, $format),
                    $viewdata ?: []
                ));
            }
        }

        return $ok;
    }
}
