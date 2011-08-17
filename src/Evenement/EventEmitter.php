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

/**
 * Handles listener attachment and event emitting
 *
 * @author Igor Wiedler
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class EventEmitter
{
    /**
     * @var array
     */
    private $listeners = array();

    /**
     * Sets all listeners for an event. This will override all previous added listeners.
     * To unset all listeners for a event use $emitter->set('event.name', array());
     *
     * @param string $event
     * @param array $listeners
     */
    public function set($event, array $listeners = array())
    {
        unset($this->listeners[$event]);

        foreach ($listeners as $listener) {
            $this->add($event, $listener);
        }
    }

    /**
     * Add a listener to an event. Optionally providing a priority
     *
     * @param string $event
     * @param callable $listener
     * @param integer $priority
     */
    public function add($event, $listener, $priority = 10)
    {
        if (!is_callable($listener)) {
            throw new \InvalidArgumentException('The provided listener was not a valid callable.');
        }

        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }

        if (!isset($this->listeners[$event][$priority])) {
            $this->listeners[$event][$priority] = array();
        }

        $this->listeners[$event][$priority][] = $listener;
    }

    /**
     * Recursively search for a listener and remove it from the list
     *
     * @param string $event
     * @param callable $listener
     */
    public function remove($event, $listener)
    {
        if (!$this->listeners[$event]) {
            throw \InvalidArgumentException('Event does not exists');
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            if (false !== ($index = array_search($listeners, $listener, true))) {
                unset($this->listeners[$event][$priority][$index]);
            }
        }
    }

    /**
     * Returns the listeners for a single event
     *
     * @param string $event
     */
    public function get($event)
    {
        if (!$this->listeners[$event]) {
            throw \InvalidArgumentException('Event does not exists');
        }

        krsort($this->listeners[$event]);
        $listeners = array();

        foreach ($this->listeners[$event] as $all) {
            $listeners = array_merge($listeners, $all);
        }

        return $listeners;
    }

    /**
     * @param string $event
     * @param array $arguments
     */
    public function emit($event, array $arguments = array())
    {
        foreach ($this->get($event) as $listener) {
            call_user_func_array($listener, $arguments);
        }
    }
}
