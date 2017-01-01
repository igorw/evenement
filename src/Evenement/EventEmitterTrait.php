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

/**
 * A series of properties and methods which allow a piece of logic to recieve, react and
 * compose responses to specific events.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
trait EventEmitterTrait
{
    
    /**
     * A list of all callables that react to and compose a response for
     * an event that occurs in the lifecycle of a service or application.
     * 
     * @var array
     */
    protected $listeners = [];

    /**
     * Register some logic (contained with in a callable, e.g. closure, method, function etc.) that
     * responds to a particular event (whether or not it actually occurs).
     * 
     * @param  String   $event    The name of the event to react to. For example "user.loggedIn"
     * @param  callable $listener The contained piece of logic that reacts to and composes a response to said event.
     * 
     * @return null
     */
    public function on($event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }

    /**
     * Register some logic (contained with in a callable, e.g. closure, method, function etc.) that
     * responds to a particular event (whether or not it actually occurs) one time only.
     * 
     * @param  String   $event   The name of the event to react to. For example "user.loggedIn".
     * @param  callable $listener The contained piece of logic that reacts to and composes a response to said event.
     * 
     * @return null
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
     * De-Register some logic (contained with in a callable, e.g. closure, method, function etc.)
     * so that it doesn't respond to a particular event.
     * 
     * @param  String   $event    The name of the event to react to. For example "user.loggedIn".
     * @param  callable $listener The contained piece of logic that reacts to and composes a response to said event.
     * 
     * @return null
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
     * De-Register all logic that has been associated with the supplied event OR all events
     * if no specific event has been supplied. This will sever the connection between the 
     * event in question and any logic being run within the context of the service or application
     * to which it has been linked.
     * 
     * @param  String $event (Optional - will deregister all events if not supplied.) The event that
     *                       you wish to be unlinked. For example "user.loggedIn".
     * 
     * @return null
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
     * Provide a list of all of the logic associated with an event
     * (contained with in callables, e.g. closure, method, function etc.).
     * 
     * @param  String   $event    The name of the event in question. For example "user.loggedIn".
     * 
     * @return array Either a list of all of the logic associated with the given event or an empty array
     *               if there is none.
     */
    public function listeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }

    /**
     * Call upon and run all logic associated within a given event.
     * 
     * @param  String   $event     The name of the event in question. For example "user.loggedIn".
     * @param  array    $arguments Any additional information that you believe ANY responding logic may need.
     * 
     * @return null
     */
    public function emit($event, array $arguments = [])
    {
        foreach ($this->listeners($event) as $listener) {
            call_user_func_array($listener, $arguments);
        }
    }
}
