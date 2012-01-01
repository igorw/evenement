<?php

/*
 * This file is part of Evenement.
 *
 * Copyright (c) 2011 Igor Wiedler
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Evenement\Tests;

use Evenement\EventEmitter2;

class EventEmitter2Test extends \PHPUnit_Framework_TestCase
{
    private $emitter;

    public function setUp()
    {
        $this->emitter = new EventEmitter2();
    }

    // matching tests from
    // test/wildcardEvents/addListener.js

    public function testWildcardMatching7()
    {
        $listenerCalled = 0;

        $listener = function () use (&$listenerCalled) {
            $listenerCalled++;
        };

        $this->emitter->on('*.test', $listener);
        $this->emitter->on('*.*', $listener);
        $this->emitter->on('*', $listener);

        $this->emitter->emit('other.emit');
        $this->emitter->emit('foo.test');

        $this->assertSame(3, $listenerCalled);
    }

    public function testWildcardMatching8()
    {
        $listenerCalled = 0;

        $listener = function () use (&$listenerCalled) {
            $listenerCalled++;
        };

        $this->emitter->on('foo.test', $listener);
        $this->emitter->on('*.*', $listener);
        $this->emitter->on('*', $listener);

        $this->emitter->emit('*.*');
        $this->emitter->emit('foo.test');
        $this->emitter->emit('*');

        $this->assertSame(5, $listenerCalled);
    }

    public function testOnAny()
    {
        $this->emitter->onAny(function () {});
    }

    public function testOnAnyWithEmit()
    {
        $listenerCalled = 0;

        $this->emitter->onAny(function () use (&$listenerCalled) {
            $listenerCalled++;
        });

        $this->assertSame(0, $listenerCalled);

        $this->emitter->emit('foo');

        $this->assertSame(1, $listenerCalled);

        $this->emitter->emit('bar');

        $this->assertSame(2, $listenerCalled);
    }

    public function testoffAnyWithEmit()
    {
        $listenerCalled = 0;

        $listener = function () use (&$listenerCalled) {
            $listenerCalled++;
        };

        $this->emitter->onAny($listener);
        $this->emitter->offAny($listener);

        $this->assertSame(0, $listenerCalled);
        $this->emitter->emit('foo');
        $this->assertSame(0, $listenerCalled);
    }

    /**
     * @dataProvider provideMany
     */
    public function testMany($amount)
    {
        $listenerCalled = 0;

        $this->emitter->many('foo', $amount, function () use (&$listenerCalled) {
            $listenerCalled++;
        });

        for ($i = 0; $i < $amount; $i++) {
            $this->assertSame($i, $listenerCalled);
            $this->emitter->emit('foo');
        }

        $this->emitter->emit('foo');
        $this->assertSame($amount, $listenerCalled);
    }

    public function provideMany()
    {
        return array(
            array(0),
            array(1),
            array(2),
            array(3),
            array(4),
            array(400),
        );
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testManyWithLessThanZeroTtl()
    {
        $this->emitter->many('foo', -1, function () {});
        $this->emitter->emit('foo');
    }
}
