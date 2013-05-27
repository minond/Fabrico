<?php

namespace Fabrico\Test\Event;

use Fabrico\Test\Test;
use Fabrico\Test\Mock\Event\PublicListeners;
use Fabrico\Event\Listeners;
use Fabrico\Core\Application;

class ListenersTest extends Test
{
    /**
     * @var Listeners
     */
    public $listeners;

    public static $loaded = false;

    public function setUp()
    {
        $this->listeners = new PublicListeners;
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidListenersCannotBeSet()
    {
        $this->listeners->setListeners([ [ 's' => 'd' ] ]);
        $this->assertEquals([], $this->listener->getListeners());
    }

    /**
     * @expectedException Exception
     * eexpectedExceptionMessage Listener "0" missing "name" property
     */
    public function testListenersIndexIsIncludeInErrorMessage()
    {
        $this->listeners->setListeners([ [ 's' => 'd' ] ]);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Listener "MyListener" missing "active" property
     */
    public function testListenersNameIsIncludeInErrorMessage()
    {
        $this->listeners->setListeners([ [ 'name' => 'MyListener' ] ]);
    }

    /**
     * @expectedException Exception
     * eexpectedExceptionMessage Listener "0" missing "name" property
     */
    public function testNamePropertyIsRequired()
    {
        $this->listeners->setListeners([ [ 's' => 'd' ] ]);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Listener "MyListener" missing "tags" property
     */
    public function testTagsPropertyIsRequired()
    {
        $this->listeners->setListeners([ [ 'name' => 'MyListener',
            'active' => false ] ]);
    }

    public function testValidListenersCanBeSet()
    {
        $listeners = [
            [
                'name' => 'MyListener',
                'tags' => [ 'test' ],
                'active' => true,
            ],
        ];

        $this->listeners->setListeners($listeners);
        $this->assertEquals($listeners, $this->listeners->getListeners());
    }

    public function testInactiveListenersAreIgnores()
    {
        $inactive_listeners = [
            [
                'name' => 'MyListener',
                'tags' => [ 'test' ],
                'active' => false,
            ],
            [
                'name' => 'MyListener',
                'tags' => [ 'test' ],
                'active' => false,
            ],
        ];

        $active_listeners = [
            [
                'name' => 'MyListener',
                'tags' => [ 'test' ],
                'active' => true,
            ],
            [
                'name' => 'MyListener',
                'tags' => [ 'test' ],
                'active' => true,
            ],
        ];

        $this->listeners
            ->setListeners(array_merge($active_listeners, $inactive_listeners));
        $this->assertEquals($active_listeners,
            $this->listeners->publicGetActiveListeners());
    }

    public function testListenersCanBeLoaded()
    {
        $app = new Application;
        $app->setRoot(FABRICO_ROOT);

        PublicListeners::setDir('tests/mocks/Event/Listeners');

        $this->listeners->setListeners([
            [
                'name' => 'set_test_class_property',
                'tags' => [ 'test' ],
                'active' => true,
            ],
        ]);

        $this->listeners->loadListeners();
        $this->assertTrue(self::$loaded);
    }

    /**
     * @expectedException Exception
     */
    public function testAnExceptionIsThrownWhenAListenersNotFound()
    {
        $this->listeners->setListeners([
            [
                'name' => 'MyListener',
                'tags' => [ 'test' ],
                'active' => true,
            ],
        ]);

        $this->listeners->loadListeners();
    }
}
