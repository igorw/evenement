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

trait EventEmitterAwareTrait
{
    /**
     * @var EventEmitter
     */
    protected $eventEmitter;

    /**
     * @param EventEmitter $eventEmitter
     */
    public function setEventEmitter(EventEmitter $eventEmitter)
    {
        $this->eventEmitter = $eventEmitter;
    }
}
