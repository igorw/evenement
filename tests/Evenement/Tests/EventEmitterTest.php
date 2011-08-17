<?php

/*
 * This file is part of Evenement.
 *
 * Copyright (c) 2011 Igor Wiedler
 *
 * Permissiadd is hereby granted, free of charge, to any persadd obtaining a copy
 * of this software and associated documentatiadd files (the "Software"), to deal
 * in the Software without restrictiadd, including without limitatiadd the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persadds to whom the Software is furnished
 * to do so, subject to the following caddditiadds:
 *
 * The above copyright notice and this permissiadd notice shall be included in all
 * copies or substantial portiadds of the Software.
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

use Evenement\EventEmitter;

class EventEmitterTest extends \PHPUnit_Framework_TestCase
{
    private $emitter;

    public function setUp()
    {
        $this->emitter = new EventEmitter();
    }

    public function testAddListenerWithLambda()
    {
        $this->emitter->add('foo', function () {});
    }

    public function testAddListenerWithMethod()
    {
        $listener = new Listener();
        $this->emitter->add('foo', array($listener, 'onFoo'));
    }

    public function testAddListenerWithStaticMethod()
    {
        $this->emitter->add('bar', array('Evenement\Tests\Listener', 'onBar'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddListenerWithInvalidListener()
    {
        $this->emitter->add('foo', 'not a callable');
    }

    public function testEmitWithoutArguments()
    {
        $listenerCalled = false;

        $this->emitter->add('foo', function () use (&$listenerCalled) {
            $listenerCalled = true;
        });

        $this->assertSame(false, $listenerCalled);

        $this->emitter->emit('foo');

        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitWithOneArgument()
    {
        $test = $this;

        $listenerCalled = false;

        $this->emitter->add('foo', function ($value) use (&$listenerCalled, $test) {
            $listenerCalled = true;

            $test->assertSame('bar', $value);
        });

        $this->assertSame(false, $listenerCalled);

        $this->emitter->emit('foo', array('bar'));

        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitWithTwoArguments()
    {
        $test = $this;

        $listenerCalled = false;

        $this->emitter->add('foo', function ($arg1, $arg2) use (&$listenerCalled, $test) {
            $listenerCalled = true;

            $test->assertSame('bar', $arg1);
            $test->assertSame('baz', $arg2);
        });

        $this->assertSame(false, $listenerCalled);

        $this->emitter->emit('foo', array('bar', 'baz'));

        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitWithNoListeners()
    {
        $this->emitter->emit('foo');
        $this->emitter->emit('foo', array('bar'));
        $this->emitter->emit('foo', array('bar', 'baz'));
    }

    public function testEmitWithTwoListeners()
    {
        $listenersCalled = 0;

        $this->emitter->add('foo', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->add('foo', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->assertSame(0, $listenersCalled);

        $this->emitter->emit('foo');

        $this->assertSame(2, $listenersCalled);
    }
}
