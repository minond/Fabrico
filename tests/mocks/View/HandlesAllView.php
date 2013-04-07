<?php

namespace Fabrico\Test\Mock\View;

use Fabrico\View\View;

class HandlesAllView extends View
{
    /**
     * content
     */
    private $content = '';

    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    public static function setExt($ext)
    {
        self::$ext = $ext;
    }

    public function exists()
    {
        return true;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function render($data = array(), $context = null)
    {
        return $this->content;
    }
}
