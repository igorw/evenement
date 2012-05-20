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

interface EventEmitterInterface
{
    function on($event, $listener);
    function once($event, $listener);
    function removeListener($event, $listener);
    function removeAllListeners($event = null);
    function listeners($event);
    function emit($event, array $arguments = array());
}
