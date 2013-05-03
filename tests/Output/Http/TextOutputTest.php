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

    public function testContentIsSentOutAsText()
    {
        $this->expectOutputString('hi');
        $this->text->setContent('hi');
        $this->text->output();
    }

    public function testContentCanBeSetAndRetrieved()
    {
        $this->text->setContent('hi');
        $this->assertEquals('hi', $this->text->getContent());
    }

    public function testContentCanBeAppended()
    {
        $this->expectOutputString('hi hi2');
        $this->text->append('hi');
        $this->text->append(' hi2');
        $this->text->output();
    }

    public function testContentCanBePrepended()
    {
        $this->expectOutputString('hi hi2');
        $this->text->append(' hi2');
        $this->text->prepend('hi');
        $this->text->output();
    }
}
