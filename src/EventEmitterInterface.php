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
    /**
     * Allows you to subscribe to an event.
     */
    public function on(string $event, callable $listener): static;

    /**
     * Convenience method that adds a listener which is guaranteed to only be
     * called once.
     */
    public function once(string $event, callable $listener): static;

    /**
     * Remove a specific listener for a specific event.
     */
    public function removeListener(string $event, callable $listener): void;

    /**
     * Remove all listeners for a specific event or all listeners all together.
     *
     * This is useful for long-running processes, where you want to remove listeners
     * in order to allow them to get garbage collected.
     */
    public function removeAllListeners(?string $event = null): void;

    /**
     * Allows you to inspect the listeners attached to an event. Particularly useful
     * to check if there are any listeners at all.
     *
     * @return array<string, array<int, (callable)>>|list<(callable)>
     */
    public function listeners(?string $event = null): array;

    /**
     * Emit an event, which will call all listeners.
     *
     * @param array<mixed> $arguments
     */
    public function emit(string $event, array $arguments = []): void;
}
