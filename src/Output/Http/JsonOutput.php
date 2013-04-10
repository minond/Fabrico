<?php

namespace Fabrico\Output\Http;

/**
 * handles text output
 */
class JsonOutput extends TextOutput
{
    /**
     * json encodes content
     * @return void
     */
    public function output()
    {
        echo json_encode($this->content);
    }

    /**
     * sets Content-Type to application/json
     * @codeCoverageIgnore
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => 'application/json'
        ];
    }
}
