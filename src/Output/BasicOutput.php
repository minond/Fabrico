<?php

namespace Fabrico\Output;

/**
 * implements Output function
 */
class BasicOutput implements Output
{
    /**
     * @var string
     */
    protected $content = '';

    /**
     * content setter
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * content getter
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function output()
    {
        echo $this->content;
    }

    /**
     * @inheritdoc
     */
    public function append($text)
    {
        $this->content = $this->content . $text;
    }

    /**
     * @inheritdoc
     */
    public function prepend($text)
    {
        $this->content = $text . $this->content;
    }
}
