<?php

namespace Fabrico\Output\Http;

/**
 * handles html output
 */
class HtmlOutput extends TextOutput
{
    /**
     * sets Content-Type to text/html
     * @codeCoverageIgnore
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => 'text/html'
        ];
    }
}