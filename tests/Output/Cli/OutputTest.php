<?php

namespace Fabrico\Test\Output\Cli;

use Fabrico\Output\Cli\Output;
use Fabrico\Test\Test;

class OutputTest extends Test
{
    public $text;

    public function setUp()
    {
        $this->text = new Output;
    }

    public function testContentIsSentOutAsText()
    {
        $this->markTestIncomplete('Incomplete tests for Cli\Output');
        // ob_start();
        // $this->text->cout('{{ clear }}');
        // $clear = ob_get_clean();
        // $this->expectOutputString($clear);
        // $this->text->cout('{{ clear }}');
    }
}
