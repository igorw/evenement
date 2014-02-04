<?php

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement\Tests;

use Evenement\EventEmitter;

class StaticEventEmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testEmitter()
    {
        $emitter = StaticEventEmitter::emitter();
        $this->assertInstanceOf('\\Evenement\\EventEmitter', $emitter);
    }

    public function testAdd()
    {
        StaticEventEmitter::on('foo', function () {});
    }

    public function testEmit()
    {
        $listenerCalled = false;

        StaticEventEmitter::on('foo', function () use (&$listenerCalled) {
            $listenerCalled = true;
        });

        $this->assertFalse($listenerCalled);
        StaticEventEmitter::emit('foo');
        $this->assertTrue($listenerCalled);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Call to undefined method Evenement\Tests\StaticEventEmitter::foo()
     */
    public function testMethodDoesNotExist()
    {
        StaticEventEmitter::foo();
    }
}
