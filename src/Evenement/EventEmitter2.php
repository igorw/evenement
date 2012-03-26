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

class EventEmitter2 extends EventEmitter
{
    protected $options;
    protected $anyListeners = array();

    public function __construct(array $options = array())
    {
        $this->options = array_merge(array(
            'delimiter' => '.',
        ), $options);
    }

    public function onAny($listener)
    {
        $this->anyListeners[] = $listener;
    }

    public function offAny($listener)
    {
        if (false !== $index = array_search($listener, $this->anyListeners, true)) {
            unset($this->anyListeners[$index]);
        }
    }

    public function many($event, $timesToListen, $listener)
    {
        $that = $this;

        $timesListened = 0;

        if ($timesToListen == 0) {
            return;
        }

        if ($timesToListen < 0) {
            throw new \OutOfRangeException('You cannot listen less than zero times.');
        }

        $manyListener = function () use ($that, &$timesListened, &$manyListener, $event, $timesToListen, $listener) {
            if (++$timesListened == $timesToListen) {
                $that->removeListener($event, $manyListener);
            }

            call_user_func_array($listener, func_get_args());
        };

        $this->on($event, $manyListener);
    }

    public function emit($event, array $arguments = array())
    {
        foreach ($this->anyListeners as $listener) {
            call_user_func_array($listener, $arguments);
        }

        parent::emit($event, $arguments);
    }

    public function listeners($event)
    {
        $matchedListeners = array();

        foreach ($this->_listeners as $name => $listeners) {
            foreach ($listeners as $listener) {
                if ($this->matchEventName($event, $name)) {
                    $matchedListeners[] = $listener;
                }
            }
        }

        return $matchedListeners;
    }

    protected function matchEventName($matchPattern, $eventName)
    {
        $patternParts = explode($this->options['delimiter'], $matchPattern);
        $nameParts = explode($this->options['delimiter'], $eventName);

        if (count($patternParts) != count($nameParts)) {
            return false;
        }

        $size = min(count($patternParts), count($nameParts));
        for ($i = 0; $i < $size; $i++) {
            $patternPart = $patternParts[$i];
            $namePart = $nameParts[$i];

            if ('*' === $patternPart || '*' === $namePart) {
                continue;
            }

            if ($namePart === $patternPart) {
                continue;
            }

            return false;
        }

        return true;
    }
}
