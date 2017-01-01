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
 * An object that contains a series of properties and methods
 * (From EventEmitterTrait) which allow a piece of logic to recieve,
 * react and compose responses to specific events.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class EventEmitter implements EventEmitterInterface
{
    use EventEmitterTrait;
}
