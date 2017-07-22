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

interface EventEmitterInterface
{
    public function on(string $event, callable $listener);
    public function once(string $event, callable $listener);
    public function removeListener(string $event, callable $listener);
    public function removeAllListeners(string $event = null);
    public function listeners(string $event);
    public function emit(string $event, array $arguments = []);
}
