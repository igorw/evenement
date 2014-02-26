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
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $mutedListeners = [];

    /**
     * @param string $event
     * @param callable $listener
     */
    public function on($event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }

    /**
     * @param string $event
     * @param callable $listener
     */
    public function once($event, callable $listener)
    {
        $onceListener = function () use (&$onceListener, $event, $listener) {
            $this->removeListener($event, $onceListener);

            call_user_func_array($listener, func_get_args());
        };

        $this->on($event, $onceListener);
    }

    /**
     * @param string $event
     * @param callable $listener
     */
    public function removeListener($event, callable $listener)
    {
        if (isset($this->listeners[$event])) {
            $index = array_search($listener, $this->listeners[$event], true);
            if (false !== $index) {
                unset($this->listeners[$event][$index]);
            }
        }
    }

    /**
     * @param string|null $event
     */
    public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners = [];
        }
    }

    /**
     * @param string $event
     * @return array
     */
    public function listeners($event)
    {
        return $this->getListeners($event);
    }

    /**
     * @param string $event
     * @return array
     */
    public function getListeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }

    /**
     * @param string $event
     * @param array $arguments
     */
    public function emit($event, array $arguments = [])
    {
        foreach ($this->listeners($event) as $listener) {
            call_user_func_array($listener, $arguments);
        }
    }

    /**
     * @param $event
     * @param callable $listener
     * @return $this
     */
    public function mute($event, callable $listener = null)
    {
        if (isset($this->listeners[$event])) {
            if (null !== $listener) {
                $index = array_search($listener, $this->listeners[$event], true);
                if (false !== $index) {
                    if (!isset($this->mutedListeners[$event])) {
                        $this->mutedListeners[$event] = [];
                    }
                    $this->mutedListeners[$event][$index] = $this->listeners[$event][$index];
                    unset($this->listeners[$event][$index]);
                }
            } else {
                if (isset($this->mutedListeners[$event])) {
                    $this->mutedListeners[$event] = array_merge($this->mutedListeners[$event], $this->listeners[$event]);
                } else {
                    $this->mutedListeners[$event] = $this->listeners[$event];
                }
                unset($this->listeners[$event]);
            }
        }
        return $this;
    }

    /**
     * @param string $event
     * @param callable $listener
     * @return $this
     */
    public function unMute($event, callable $listener = null)
    {
        if (isset($this->mutedListeners[$event])) {
            if (null !== $listener) {
                $index = array_search($listener, $this->mutedListeners[$event], true);
                if (false !== $index) {
                    if (!isset($this->listeners[$event])) {
                        $this->listeners[$event] = [];
                    }
                    $this->listeners[$event][$index] = $this->mutedListeners[$event][$index];
                    unset($this->mutedListeners[$event][$index]);
                }
            } else {
                if (isset($this->listeners[$event])) {
                    $this->listeners[$event] = array_merge($this->listeners[$event], $this->mutedListeners[$event]);
                } else {
                    $this->listeners[$event] = $this->mutedListeners[$event];
                }
                unset($this->mutedListeners[$event]);
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function muteAll()
    {
        foreach ($this->listeners as $event => $listeners) {
            if (isset($this->mutedListeners[$event])) {
                $this->mutedListeners[$event] = array_merge($this->mutedListeners[$event], $listeners);
            } else {
                $this->mutedListeners[$event] = $listeners;
            }
            unset($this->listeners[$event]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function unMuteAll()
    {
        foreach ($this->mutedListeners as $event => $listeners) {
            if (isset($this->listeners[$event])) {
                $this->listeners[$event] = array_merge($this->listeners[$event], $listeners);
            } else {
                $this->listeners[$event] = $listeners;
            }
            unset($this->mutedListeners[$event]);
        }
        return $this;
    }
}
