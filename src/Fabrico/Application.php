<?php

namespace Fabrico;

use Closure;
use StdClass;
use Exception;
use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\RuleBook;
use Efficio\Configurare\Configuration;
use Efficio\Cache\RuntimeCache;
use Twig_Environment as TwigEnv;
use Twig_Loader_Filesystem as TwigFs;
use Fabrico\Error\Renderer\NoViewsFoundException;

class Application
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
        self::$app = $app;
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
     * retrieve a base object or a property value from a core component:
     * req.id => Request->id
     * @param string $path
     * @return mixed
     */
    public static function get($path)
    {
        $path = explode('.', $path);
        $root = array_shift($path);
        $base = self::$app->{ $root };
        $rval = $base;

        foreach ($path as $prop) {
            $rval = is_array($rval) ? $rval[ $prop ] : $rval->{ $prop };
        }

        return $rval;
    }

    /**
     * @param string $namespace_name
     * @param string $controller_name
     * @return string
     */
    protected function getViewFileDirectory($namespace_name, $controller_name = '')
    {
        $conf = $this->getConfiguration();
        return $conf->get('app:namespace') === $namespace_name ?
            sprintf('views/%s/', strtolower($controller_name)) :
            sprintf('lib/%s/views/%s/', $namespace_name, strtolower($controller_name));
    }

    /**
     * handle a request
     */
    public function handle()
    {
        if ($route = $this->rules->matching($this->req, true)) {
            $action_name = $route['action'];
            $controller_name = $route['controller'];
            $namespace_name = $route['namespace'];

            $controller = sprintf('%s\\Controller\\%s', $namespace_name, ucwords($controller_name));
            $view_dir = $this->getViewFileDirectory($namespace_name, $controller_name);
            $view_base = str_replace('//', '/', $this->getViewFileDirectory($namespace_name));

            if (class_exists($controller)) {
                $controller = new $controller;

                if (method_exists($controller, $action_name) && is_callable([ $controller, $action_name ])) {
                    $out = $controller->{ $action_name }($this->req, $this->res);
                    $this->res->setStatusCode(Status::OK);

                    try {
                        $str = $this->renderer->render(
                            sprintf('%s%s.%s', $view_dir, $action_name, 'html'),
                            $out
                        );
                    } catch (NoViewsFoundException $no_view_found) {
                        $str = $this->renderer->render(
                            sprintf('%s%s.%s', $view_dir, 'default', 'html'),
                            $out
                        );
                    }

                    $this->res->setContent($str);
                }
            }
        }
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
}

