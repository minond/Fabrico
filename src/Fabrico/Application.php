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
     * @param Application
     */
    private static $app;

    /**
     * sets up some application properties
     * @param Application $app
     */
    public static function wire(Application $app)
    {
        $app->req = $app->getRequest();
        $app->res = $app->getResponse();
        $app->conf = $app->getConfiguration();
    }

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
    protected function getControllerName($namespace_name, $controller_name)
    {
        return sprintf(
            '%s\\Controller\\%s',
            $namespace_name,
            ucwords($controller_name)
        );
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
     * @param string $dir
     * @param string $name
     * @param StdClass $data, default = null
     * @return string
     */
    protected function getViewFile($dir, $name, StdClass $data = null)
    {
        $files = glob("{$dir}{$name}.html{,.php,.twig}", GLOB_BRACE);
        $content = '';

        switch (count($files)) {
            case 1:
                $file = $files[0];
                $extension = substr($file, strrpos($file, '.') + 1);

                switch ($extension) {
                    case 'php':
                        $content = call_user_func(Closure::bind(function() use ($file) {
                            ob_start();
                            require $file;
                            return ob_get_clean();
                        }, $data ?: new StdClass));
                        break;

                    case 'twig':
                        $dir = dirname($dir);
                        $fs = new TwigFs($dir);
                        $fs->addPath($dir, 'app');
                        $twig = new TwigEnv($fs);

                        if (file_exists('init/twig.php')) {
                            call_user_func(function() use(& $twig, & $fs) {
                                require_once 'init/twig.php';
                            });
                        }

                        $template = $twig->loadTemplate(str_replace($dir, '', $file));
                        $content = $template->render((array) $data);
                        break;

                    case 'html':
                    default:
                        $content = file_get_contents($file);
                        break;
                }
                break;

            case 0:
                $content = false;
                break;

            default:
                throw new Exception('Multiple view files found: ' .
                    implode(', ', $files));
        }

        return $content;
    }

    /**
     * handle a request
     */
    public function handle()
    {
        $req = $this->getRequest();
        $res = $this->getResponse();
        $rules = $this->getRuleBook();

        if ($route = $rules->matching($req, true)) {
            $action_name = $route['action'];
            $controller_name = $route['controller'];
            $namespace_name = $route['namespace'];

            $controller = $this->getControllerName($namespace_name, $controller_name);
            $view_dir = $this->getViewFileDirectory($namespace_name, $controller_name);
            $view_base = str_replace('//', '/', $this->getViewFileDirectory($namespace_name));

            if (class_exists($controller)) {
                $controller = new $controller;

                if (method_exists($controller, $action_name) && is_callable([ $controller, $action_name ])) {
                    $out = $controller->{ $action_name }($req, $res);
                    $res->setStatusCode(Status::OK);

                    if (!$res->getContent()) {
                        $out = is_array($out) ? (object) $out : $out;
                        $str = $this->getViewFile($view_dir, $action_name, $out);

                        if ($str === false) {
                            $str = $this->getViewFile($view_base, 'default', $out);
                        }

                        if ($str === false) {
                            $res->setContentType(Response::TEXT);
                            $str = "view not found." .
                                   "\nbase: $view_base" .
                                   "\ndir: $view_dir" .
                                   "\ncontroller: " . get_class($controller) .
                                   "\naction: $action_name" .
                                   "\nrequest: " . print_r($req, true);
                        }

                        $res->setContent($str);

                        // if (isset($controller->responds_to)) {
                        //     $res->setContent($out);
                        //     $res->setContentType($controller->responds_to[0]);
                        // } else {
                        //     $out = is_array($out) ? (object) $out : $out;
                        //     $res->setContent($this->getViewFile($view_dir, $action_name, $out));
                        // }
                    }
                }
            }
        }
    }

    /**
     * send the request to the client
     */
    public function send()
    {
        $req = $this->getRequest();
        $res = $this->getResponse();

        if ($res->getStatusCode() === Status::NOT_FOUND) {
            $res->setContentType(Response::TEXT);
            $res->setContent('404, not found...');
        }

        $res->sendHeaders();
        $res->sendContent();
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
}

