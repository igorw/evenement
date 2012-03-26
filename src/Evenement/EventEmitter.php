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

namespace Evenement;

class EventEmitter
{
    protected $_listeners = array();

    public function on($event, $listener)
    {
        if (!is_callable($listener)) {
            throw new \InvalidArgumentException('The provided listener was not a valid callable.');
        }

        if (!isset($this->_listeners[$event])) {
            $this->_listeners[$event] = array();
        }

        $this->_listeners[$event][] = $listener;
    }

    public function once($event, $listener)
    {
        $that = $this;

        $onceListener = function () use ($that, &$onceListener, $event, $listener) {
            $that->removeListener($event, $onceListener);

            call_user_func_array($listener, func_get_args());
        };

        $this->on($event, $onceListener);
    }

    public function removeListener($event, $listener)
    {
        if (isset($this->_listeners[$event])) {
            if (false !== $index = array_search($listener, $this->_listeners[$event], true)) {
                unset($this->_listeners[$event][$index]);
            }
        }
    }

    public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            unset($this->_listeners[$event]);
        } else {
            $this->_listeners = array();
        }
    }

    public function listeners($event)
    {
        return isset($this->_listeners[$event]) ? $this->_listeners[$event] : array();
    }

    public function emit($event, array $arguments = array())
    {
        foreach ($this->listeners($event) as $listener) {
            call_user_func_array($listener, $arguments);
        }
    }
}
