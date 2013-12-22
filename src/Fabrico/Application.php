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
     * @param Response
     */
    protected $res;

    /**
     * @param Request
     */
    protected $req;

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
     * @return Response
     */
    public function getResponse()
    {
        return $this->res;
    }

    /**
     * @param Response $res
     */
    public function setResponse(Response $res)
    {
        $this->res = $res;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->req;
    }

    /**
     * @param Request $req
     */
    public function setRequest(Request $req)
    {
        $this->req = $req;
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
            } else if (class_exists($standard_init)) {
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
     */
    public function route()
    {
        $found = false;

        if ($route = $this->rules->matching($this->req, true)) {
            // defaults
            $route = array_merge([
                'format' => 'html',

                // can be a static resource
                'directory' => '',
                'file' => '',

                // or an action call
                'action' => '',
                'controller' => '',
                'namespace' => $this->conf->get('app:namespace'),
            ], $route);

            // route info
            $directory = $route['directory'];
            $file = $route['file'];
            $format = $route['format'];
            $action = $route['action'];
            $namespace = $route['namespace'];
            $controller = $route['controller'];

            // views holder and controller name
            $views = sprintf('views/%s/', strtolower($controller));
            $controller = sprintf('%s\Controller\%s', $namespace, $controller);

            // valid controller and action?
            $found = class_exists($controller) &&
                method_exists($controller, $action) &&
                is_callable([ $controller, $action ]);

            if ($found) {
                $controller = new $controller;
                $viewdata = $controller->{ $action }($this->req, $this->res);

                if (!$this->res->getStatusCode()) {
                    $this->res->setStatusCode(Status::OK);
                }

                if (!$this->res->getContent()) {
                    $this->res->setContent($this->renderer->render(
                        $this,
                        sprintf('%s%s.%s', $views, $action, $format),
                         $viewdata ?: []
                    ));
                }
            }
        }

        return $found;
    }
}

