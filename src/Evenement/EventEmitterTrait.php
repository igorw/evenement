<?php

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement;

trait EventEmitterTrait
{
    protected $listeners = [];

    public function on($event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $index = static::buildIndex($listener);
        $this->listeners[$event][$index] = $listener;
    }

    public function once($event, callable $listener)
    {
        $onceListener = function () use (&$onceListener, $event, $listener) {
            $this->removeListener($event, $onceListener);

            call_user_func_array($listener, func_get_args());
        };

        $this->on($event, $onceListener);
    }

    public function removeListener($event, callable $listener)
    {
        if (isset($this->listeners[$event])) {
            $index = static::buildIndex($listener);
            if (isset($this->listeners[$event][$index])) {
                unset($this->listeners[$event][$index]);
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
    }

    public function listeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }

    public function emit($event, array $arguments = [])
    {
        foreach ($this->listeners($event) as $listener) {
            call_user_func_array($listener, $arguments);
        }
    }

    protected static function buildIndex($listener)
    {
        if (is_string($listener)) {
            return $listener;
        }

        if (is_array($listener)) {
            $key = '';
            foreach ($listener as $part) {
                $key .= self::buildIndex($part);
            }
            return $key;
        }

        return spl_object_hash($listener);
    }
}
