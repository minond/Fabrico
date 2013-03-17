<?php

namespace Fabrico\Test\Output;

use Fabrico\Output\JsonOutput;
use Fabrico\Test\Test;

class JsonOutputTest extends Test
{
    public $json;

    public function setUp()
    {
        $this->json = new JsonOutput;
    }

    public function testContentIsSentOutAsText()
    {
        $content = [
            'random' => 'data',
            'list' => [1, 2, 3]
        ];

        $this->expectOutputString(json_encode($content));
        $this->json->setContent($content);
        $this->json->output();
    }
}
