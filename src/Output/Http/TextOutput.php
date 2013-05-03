<?php

namespace Fabrico\Output\Http;

use Fabrico\Output\BasicOutput;
use Fabrico\Output\Output as OutputBase;

/**
 * handles text output
 */
class TextOutput extends BasicOutput implements Output, OutputBase
{
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
