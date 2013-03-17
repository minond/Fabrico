<?php

namespace Fabrico\Request;

use Fabrico\Response\HttpResponse;
use Fabrico\Output\TextOutput;
use Fabrico\Output\HtmlOutput;
use Fabrico\Output\JsonOutput;

/**
 * handles all http requests
 */
class HttpRequest implements Request
{
    /**
     * .html - default
     */
    const HTML = 'html';

    /**
     * .json
     */
    const JSON = 'json';

    /**
     * .text
     */
    const TEXT = 'text';

    /**
     * request parameters
     * @var array
     */
    private $data;

    /**
     * view file requested
     * @var string
     */
    private $view_file;

    /**
     * controller requested
     * @var string
     */
    private $controller;

    /**
     * method requested
     * @var string
     */
    private $method;

    /**
     * action requested
     * @var string
     */
    private $action;

    /**
     * requested format
     * @var string
     */
    private $format;

    /**
     * sets default format to HTML
     */
    public function __construct()
    {
        $this->format = self::HTML;
    }

    /**
     * gives access to $data values
     * @param string $var
     * @return mixed
     */
    public function __get($var)
    {
        return array_key_exists($var, $this->data) ?
            $this->data[ $var ] : null;
    }

    /**
     * gives access to $data values
     * @param string $var
     * @param mixed $val
     */
    public function __set($var, $val)
    {
        return array_key_exists($var, $this->data) ?
            $this->data[ $var ] = $val : null;
    }

    /**
     * view file setter
     * @param string $file
     */
    public function setViewFile($file)
    {
        $parts = explode('.', $file);
        $this->view_file = $parts[0];

        if (isset($parts[1])) {
            $this->setFormat($parts[1]);
        }
    }

    /**
     * view file getter
     * @return string
     */
    public function getViewFile()
    {
        return $this->view_file;
    }

    /**
     * controller setter
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * controller getter
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * method setter
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * method getter
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * action setter
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * action getter
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * format setter
     * @param string $format
     * @throws \Exception
     */
    public function setFormat($format)
    {
        if (!in_array($format, [ self::TEXT, self::JSON, self::HTML ])) {
            throw new \Exception("Invalid format: {$format}");
        }

        $this->format = $format;
    }

    /**
     * format getter
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * we'll require one of the following (in this order):
     * # a controller action
     * # a controller method
     * # a view file
     * @return boolean
     */
    public function valid()
    {
        $valid = false;

        if ($this->format) {
            if ($this->controller && $this->action) {
                $valid = true;
            } else if ($this->controller && $this->method) {
                $valid = true;
            } else if ($this->view_file) {
                $valid = true;
            }
        }

        return $valid;
    }

    /**
     * data setter
     * @param array $data
     */
    public function setData(array & $data)
    {
        $this->data = & $data;
    }

    /**
     * data getter
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function respondWith()
    {
        $res = new HttpResponse;
        $out = null;

        switch ($this->format) {
            case self::JSON:
                $out = new JsonOutput;
                break;

            case self::HTML:
                $out = new HtmlOutput;
                break;

            case self::TEXT:
            default:
                $out = new TextOutput;
                break;
        }

        if ($out) {
            $res->setOutput($out);
        }

        return $res;
    }
}
