<?php

namespace Fabrico\Output\Http;

use Fabrico\Output\Output as OutputBase;

/**
 * handles text output
 */
class TextOutput implements Output, OutputBase
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

    /**
     * sets Content-Type to text
     * @codeCoverageIgnore
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => 'text'
        ];
    }
}
