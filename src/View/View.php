<?php

namespace Fabrico\View;

use Fabrico\Core\Application;
use Fabrico\Project\FileFinder;
use Fabrico\Event\Observable;
use Fabrico\Event\Listener;

/**
 * retrieves and renders view files
 */
class View
{
    use FileFinder, Observable;

    /**
     * @see Fabrico\Project\FileFilder
     */
    protected static $ext = '.php';

    /**
     * @see Fabrico\Project\FileFilder
     */
    protected static $dir = 'views';

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
        $content = '';
        $fparts = explode('.', $this->file);
        $extension = count($fparts) !== 1 ? array_pop($fparts) : null;
        $file = self::generateFileFilderFilePath($this->file);

        $args = (object) [
            'view' => & $this,
            'data' => & $data,
            'context' => & $context,
            'content' => & $content,
            'file' => & $this->file,
            'filepath' => & $file,
            'extension' => $extension,
            'filename' => self::generateFileFilderFileName($this->file),
            'dirpath' => self::generateFileFilderDirectoryPath(),
        ];

        $this->signal(__FUNCTION__, Listener::PRE,[ & $args ]);

        // so view files don't get access to the View object
        if (!$content) {
            $content = call_user_func(\Closure::bind(function() use (& $data, $file) {
                ob_start();
                extract($data);
                require($file);
                return ob_get_clean();
            }, $context));
        }

        $this->signal(__FUNCTION__, Listener::POST, [ & $args ]);
        return $content;
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
