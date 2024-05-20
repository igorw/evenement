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

use function array_keys;
use function array_merge;
use function array_search;
use function array_unique;
use function array_values;
use function count;

trait EventEmitterTrait
{
    /**
     * @var array<string, array<int, (callable)>>
     */
    protected array $listeners = [];

    /**
     * @var array<string, array<int, (callable)>>
     */
    protected array $onceListeners = [];

    public function on(string $event, callable $listener): static
    {
        if ($event === '') {
            throw new InvalidArgumentException('event name must not be an empty string');
        }

        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    public function once(string $event, callable $listener): static
    {
        if ($event === '') {
            throw new InvalidArgumentException('event name must not be an empty string');
        }

        if (!isset($this->onceListeners[$event])) {
            $this->onceListeners[$event] = [];
        }

        $this->onceListeners[$event][] = $listener;

        return $this;
    }

    public function removeListener(string $event, callable $listener): void
    {
        if ($event === '') {
            throw new InvalidArgumentException('event name must not be an empty string');
        }

        if (isset($this->listeners[$event])) {
            $index = array_search($listener, $this->listeners[$event], true);

            if (false !== $index) {
                unset($this->listeners[$event][$index]);

                if (count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }
            }
        }

        if (isset($this->onceListeners[$event])) {
            $index = array_search($listener, $this->onceListeners[$event], true);

            if (false !== $index) {
                unset($this->onceListeners[$event][$index]);

                if (count($this->onceListeners[$event]) === 0) {
                    unset($this->onceListeners[$event]);
                }
            }
        }
    }

    public function removeAllListeners(?string $event = null): void
    {
        if ($event !== null) {
            unset($this->listeners[$event], $this->onceListeners[$event]);
        } else {
            $this->listeners = [];
            $this->onceListeners = [];
        }
    }

    public function listeners(?string $event = null): array
    {
        if ($event === null) {
            $events = [];
            $eventNames = array_unique(
                array_merge(
                    array_keys($this->listeners),
                    array_keys($this->onceListeners)
                )
            );

            foreach ($eventNames as $eventName) {
                $events[$eventName] = array_merge(
                    $this->listeners[$eventName] ?? [],
                    $this->onceListeners[$eventName] ?? []
                );
            }

            return $events;
        }

        return array_merge(
            $this->listeners[$event] ?? [],
            $this->onceListeners[$event] ?? []
        );
    }

    public function emit(string $event, array $arguments = []): void
    {
        if ($event === '') {
            throw new InvalidArgumentException('event name must not be an empty string');
        }

        $listeners = [];
        if (isset($this->listeners[$event])) {
            $listeners = array_values($this->listeners[$event]);
        }

        $onceListeners = [];
        if (isset($this->onceListeners[$event])) {
            $onceListeners = array_values($this->onceListeners[$event]);
        }

        if ($listeners !== []) {
            foreach ($listeners as $listener) {
                $listener(...$arguments);
            }
        }

        if ($onceListeners !== []) {
            unset($this->onceListeners[$event]);

            foreach ($onceListeners as $listener) {
                $listener(...$arguments);
            }
        }
    }
}
