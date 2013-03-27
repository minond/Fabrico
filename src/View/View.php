<?php

namespace Fabrico\View;

use Fabrico\Core\Application;
use Fabrico\Project\FileFinder;

/**
 * retrieves and renders view files
 */
class View
{
    use FileFinder;

    /**
     * @see Fabrico\Project\FileFilder
     */
    private static $ext = '.php';

    /**
     * @see Fabrico\Project\FileFilder
     */
    private static $dir = 'views';

    /**
     * view file name
     * @var string
     */
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file = null)
    {
        $this->file = $file;
    }

    /**
     * file setter
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * file getter
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * checks if the view file exists in the project
     * @return boolead
     */
    public function exists()
    {
        return self::hasProjectFile($this->file);
    }

    /**
     * load and generate a view file. return its contents
     * @param array $data
     * @param mixed $context
     * @return string
     */
    public function render($data = array(), $context = null)
    {
        $file = self::generateFileFilderFilePath($this->file);

        // so view files don't get access to the View object
        return call_user_func(\Closure::bind(function() use (& $data, $file) {
            ob_start();
            extract($data);
            require($file);
            return ob_get_clean();
        }, $context));
    }

    /**
     * generate and renders a view
     * @param string $file
     * @param array $data
     * @param mixed $context
     * @throws \Exception
     * @return string
     */
    public static function generate($file, $data = array(), $context = null)
    {
        $view = new self($file);

        if (!$view->exists()) {
            throw new \Exception("View file not found: {$file}");
        }

        return $view->render($data, $context);
    }
}
