<?php

namespace Fabrico\Test\Output\Http;

use Fabrico\Output\Http\TextOutput;
use Fabrico\Test\Test;

class TextOutputTest extends Test
{
    public $text;

    public function setUp()
    {
        $this->text = new TextOutput;
    }

    public function testContentCanBeSetAndRetrieved()
    {
        $this->text->setContent('hi');
        $this->assertEquals('hi', $this->text->getContent());
    }

    public function testContentIsSentOutAsText()
    {
        $this->expectOutputString('hi');
        $this->text->setContent('hi');
        $this->text->output();
    }
}
