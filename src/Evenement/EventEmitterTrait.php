<?php declare(strict_types=1);

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement;

use InvalidArgumentException;

trait EventEmitterTrait
{
    protected $listeners = [];
    protected $beforeOnceListeners = [];
    protected $onceListeners = [];
    protected $children = [];

    public function on($event, callable $listener)
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    public function onceBefore($event, callable $listener)
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        if (!isset($this->beforeOnceListeners[$event])) {
            $this->beforeOnceListeners[$event] = [];
        }

        $this->beforeOnceListeners[$event][] = $listener;

        return $this;
    }

    public function once($event, callable $listener)
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        if (!isset($this->onceListeners[$event])) {
            $this->onceListeners[$event] = [];
        }

        $this->onceListeners[$event][] = $listener;

        return $this;
    }

    public function off($event, callable $listener = null)
    {
        if ($listener !== null) {
            $this->removeListener($event, $listener);
            return;
        }

        $this->removeAllListeners($event);
    }

    public function eventNames() {
        return \array_unique(
            \array_merge(\array_keys($this->listeners), \array_keys($this->onceListeners), \array_keys($this->beforeOnceListeners))
        );
    }

    public function removeListener($event, callable $listener)
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        if (isset($this->listeners[$event])) {
            $index = \array_search($listener, $this->listeners[$event], true);
            if (false !== $index) {
                unset($this->listeners[$event][$index]);
                if (\count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }
            }
        }

        if (isset($this->onceListeners[$event])) {
            $index = \array_search($listener, $this->onceListeners[$event], true);
            if (false !== $index) {
                unset($this->onceListeners[$event][$index]);
                if (\count($this->onceListeners[$event]) === 0) {
                    unset($this->onceListeners[$event]);
                }
            }
        }

        if (isset($this->beforeOnceListeners[$event])) {
            $index = \array_search($listener, $this->beforeOnceListeners[$event], true);
            if (false !== $index) {
                unset($this->beforeOnceListeners[$event][$index]);
                if (\count($this->beforeOnceListeners[$event]) === 0) {
                    unset($this->beforeOnceListeners[$event]);
                }
            }
        }
    }

    public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners = [];
        }

        if ($event !== null) {
            unset($this->onceListeners[$event]);
        } else {
            $this->onceListeners = [];
        }

        if ($event !== null) {
            unset($this->beforeOnceListeners[$event]);
        } else {
            $this->beforeOnceListeners = [];
        }
    }

    public function listeners($event = null): array
    {
        if ($event === null) {
            $events = [];
            $eventNames = $this->eventNames();
            foreach ($eventNames as $eventName) {
                $events[$eventName] = \array_merge(
                    isset($this->listeners[$eventName]) ? $this->listeners[$eventName] : [],
                    isset($this->onceListeners[$eventName]) ? $this->onceListeners[$eventName] : [],
                    isset($this->beforeOnceListeners[$eventName]) ? $this->beforeOnceListeners[$eventName] : []
                );
            }
            return $events;
        }

        return \array_merge(
            isset($this->listeners[$event]) ? $this->listeners[$event] : [],
            isset($this->onceListeners[$event]) ? $this->onceListeners[$event] : [],
            isset($this->beforeOnceListeners[$event]) ? $this->beforeOnceListeners[$event] : []
        );
    }

    public function emit($event, array $arguments = [])
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        $beforeOnceListeners = [];
        if (isset($this->beforeOnceListeners[$event])) {
            $beforeOnceListeners = array_values($this->beforeOnceListeners[$event]);
        }

        $listeners = [];
        if (isset($this->listeners[$event])) {
            $listeners = array_values($this->listeners[$event]);
        }

        $onceListeners = [];
        if (isset($this->onceListeners[$event])) {
            $onceListeners = array_values($this->onceListeners[$event]);
        }

        if(empty($beforeOnceListeners) === false) {
            unset($this->beforeOnceListeners[$event]);
            foreach ($beforeOnceListeners as $listener) {
                $listener(...$arguments);
            }
        }

        if(empty($listeners) === false) {
            foreach ($listeners as $listener) {
                $listener(...$arguments);
            }
        }

        if(empty($onceListeners) === false) {
            unset($this->onceListeners[$event]);
            foreach ($onceListeners as $listener) {
                $listener(...$arguments);
            }
        }

        foreach ($this->children as $child) {
            $child->emit($event, $arguments);
        }
    }

    public function forward(EventEmitterInterface $emitter)
    {
        $this->children[] = $emitter;
    }
}
