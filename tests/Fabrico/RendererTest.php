<?php

namespace Fabrico\Tests;

use Fabrico\Renderer;
use PHPUnit_Framework_TestCase;

class RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Renderer
     */
    protected $renderer;

    public function setUp()
    {
        $this->renderer = new Renderer;
    }

    public function testHandlersCanBeAdded()
    {
        $this->renderer->handler('txt', function () {
        });
        $this->assertTrue(true);
    }

    public function testMultipleHandlersCanBeAdded()
    {
        $this->renderer->handlers([
            'txt1' => function () {
            },
            'txt2' => function () {
            },
            'txt3' => function () {
            },
        ]);
        $this->assertTrue(true);
    }
}
