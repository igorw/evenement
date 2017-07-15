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
    protected $removeOnEmit = [];

    public function on($event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    public function once($event, callable $listener)
    {
        $this->on($event, $listener);

        if (!isset($this->removeOnEmit[$event])) {
            $this->removeOnEmit[$event] = [];
        }

        $index = count($this->listeners[$event]) - 1;
        $this->removeOnEmit[$event][$index] = true;
    }

    public function removeListener($event, callable $listener)
    {
        if (isset($this->listeners[$event])) {
            $index = array_search($listener, $this->listeners[$event], true);
            if (false !== $index) {
                unset(
                    $this->listeners[$event][$index],
                    $this->removeOnEmit[$event][$index]
                );
            }
        }
    }

    public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            unset(
                $this->listeners[$event],
                $this->removeOnEmit[$event]
            );
        } else {
            $this->listeners = [];
            $this->removeOnEmit = [];
        }
    }

    public function listeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }

    public function emit($event, array $arguments = [])
    {
        foreach ($this->listeners($event) as $index => $listener) {
            call_user_func_array($listener, $arguments);
            if (isset($this->removeOnEmit[$event][$index])) {
                unset(
                    $this->listeners[$event][$index],
                    $this->removeOnEmit[$event][$index]
                );
            }
        }
    }
}
