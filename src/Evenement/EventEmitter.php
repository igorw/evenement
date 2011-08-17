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
	private $listeners = array();

	public function on($event, $listener)
	{
		if (!is_callable($listener)) {
			throw new \InvalidArgumentException('The provided listener was not a valid callable.');
		}

		if (!isset($this->listeners[$event])) {
			$this->listeners[$event] = array();
		}

		$this->listeners[$event][] = $listener;

		$this->modified[$event] = true;
	}

	public function removeListener($event, $listener)
	{
		if (isset($this->listeners[$event])) {
			if (false !== $index = array_search($listener, $this->listeners[$event], true)) {
				unset($this->listeners[$event][$index]);
			}
		}
	}

	public function removeAllListeners($event)
	{
		unset($this->listeners[$event]);
	}

	public function listeners($event)
	{
		return isset($this->listeners[$event]) ? $this->listeners[$event] : array();
	}

	public function emit($event, array $arguments = array())
	{
		foreach ($this->listeners($event) as $listener) {
			call_user_func_array($listener, $arguments);
		}
	}
}
