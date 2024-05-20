<?php declare(strict_types=1);

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement\Tests;

use Evenement\EventEmitter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventEmitter::class)]
class EventEmitterTest extends TestCase
{
    private EventEmitter $emitter;

    #[Before]
    public function setUpEmitter(): void
    {
        $this->emitter = new EventEmitter();
    }

    public function testAddListenerWithLambda(): void
    {
        $this->emitter->on('foo', static function (): void {});

        self::assertCount(1, $this->emitter->listeners('foo'));
    }

    public function testAddListenerWithMethod(): void
    {
        $listener = new Listener();
        $this->emitter->on('foo', [$listener, 'onFoo']);

        self::assertCount(1, $this->emitter->listeners('foo'));
    }

    public function testAddListenerWithStaticMethod(): void
    {
        $this->emitter->on('bar', ['Evenement\Tests\Listener', 'onBar']);

        self::assertCount(1, $this->emitter->listeners('bar'));
    }

    public function testAddListenerWithInvalidListener(): void
    {
        $this->expectException(\TypeError::class);
        $this->emitter->on('foo', 'not a callable');
    }

    public function testOnce(): void
    {
        $listenerCalled = 0;

        $this->emitter->once('foo', static function () use (&$listenerCalled): void {
            $listenerCalled++;
        });

        self::assertSame(0, $listenerCalled);

        $this->emitter->emit('foo');

        self::assertSame(1, $listenerCalled);

        $this->emitter->emit('foo');

        self::assertSame(1, $listenerCalled);
    }

    public function testOnceWithArguments(): void
    {
        $capturedArgs = [];

        $this->emitter->once('foo', static function (string $a, string $b) use (&$capturedArgs): void {
            $capturedArgs = [$a, $b];
        });

        $this->emitter->emit('foo', ['a', 'b']);

        self::assertSame(['a', 'b'], $capturedArgs);
    }

    public function testEmitWithoutArguments(): void
    {
        $listenerCalled = false;

        $this->emitter->on('foo', static function () use (&$listenerCalled): void {
            $listenerCalled = true;
        });

        self::assertFalse($listenerCalled);
        $this->emitter->emit('foo');
        self::assertTrue($listenerCalled);
    }

    public function testEmitWithOneArgument(): void
    {
        $listenerCalled = false;

        $this->emitter->on('foo', static function (string $value) use (&$listenerCalled): void {
            $listenerCalled = true;

            self::assertSame('bar', $value);
        });

        self::assertFalse($listenerCalled);
        $this->emitter->emit('foo', ['bar']);
        self::assertTrue($listenerCalled);
    }

    public function testEmitWithTwoArguments(): void
    {
        $listenerCalled = false;

        $this->emitter->on('foo', static function (string $arg1, string $arg2) use (&$listenerCalled): void {
            $listenerCalled = true;

            self::assertSame('bar', $arg1);
            self::assertSame('baz', $arg2);
        });

        self::assertFalse($listenerCalled);
        $this->emitter->emit('foo', ['bar', 'baz']);
        self::assertTrue($listenerCalled);
    }

    #[DoesNotPerformAssertions]
    public function testEmitWithNoListeners(): void
    {
        $this->emitter->emit('foo');
        $this->emitter->emit('foo', ['bar']);
        $this->emitter->emit('foo', ['bar', 'baz']);
    }

    public function testEmitWithTwoListeners(): void
    {
        $listenersCalled = 0;

        $this->emitter->on('foo', static function () use (&$listenersCalled): void {
            $listenersCalled++;
        });

        $this->emitter->on('foo', static function () use (&$listenersCalled): void {
            $listenersCalled++;
        });

        self::assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        self::assertSame(2, $listenersCalled);
    }

    public function testRemoveListenerMatching(): void
    {
        $listenersCalled = 0;

        $listener = static function () use (&$listenersCalled): void {
            $listenersCalled++;
        };

        $this->emitter->on('foo', $listener);
        $this->emitter->removeListener('foo', $listener);

        self::assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        self::assertSame(0, $listenersCalled);
    }

    public function testRemoveListenerNotMatching(): void
    {
        $listenersCalled = 0;

        $listener = static function () use (&$listenersCalled): void {
            $listenersCalled++;
        };

        $this->emitter->on('foo', $listener);
        $this->emitter->removeListener('bar', $listener);

        self::assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        self::assertSame(1, $listenersCalled);
    }

    public function testRemoveAllListenersMatching(): void
    {
        $listenersCalled = 0;

        $this->emitter->on('foo', static function () use (&$listenersCalled): void {
            $listenersCalled++;
        });

        $this->emitter->removeAllListeners('foo');

        self::assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        self::assertSame(0, $listenersCalled);
    }

    public function testRemoveAllListenersNotMatching(): void
    {
        $listenersCalled = 0;

        $this->emitter->on('foo', static function () use (&$listenersCalled): void {
            $listenersCalled++;
        });

        $this->emitter->removeAllListeners('bar');

        self::assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        self::assertSame(1, $listenersCalled);
    }

    public function testRemoveAllListenersWithoutArguments(): void
    {
        $listenersCalled = 0;

        $this->emitter->on('foo', static function () use (&$listenersCalled): void {
            $listenersCalled++;
        });

        $this->emitter->on('bar', static function () use (&$listenersCalled): void {
            $listenersCalled++;
        });

        $this->emitter->removeAllListeners();

        self::assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        $this->emitter->emit('bar');
        self::assertSame(0, $listenersCalled);
    }

    public function testCallablesClosure(): void
    {
        $calledWith = null;

        $this->emitter->on('foo', static function (?string $data) use (&$calledWith): void {
            $calledWith = $data;
        });

        $this->emitter->emit('foo', ['bar']);

        self::assertSame('bar', $calledWith);
    }

    public function testCallablesClass(): void
    {
        $listener = new Listener();
        $this->emitter->on('foo', [$listener, 'onFoo']);

        $this->emitter->emit('foo', ['bar']);

        self::assertSame(['bar'], $listener->getData());
    }


    public function testCallablesClassInvoke(): void
    {
        $listener = new Listener();
        $this->emitter->on('foo', $listener);

        $this->emitter->emit('foo', ['bar']);

        self::assertSame(['bar'], $listener->getMagicData());
    }

    public function testCallablesStaticClass(): void
    {
        $this->emitter->on('foo', '\Evenement\Tests\Listener::onBar');

        $this->emitter->emit('foo', ['bar']);

        self::assertSame(['bar'], Listener::getStaticData());
    }

    public function testCallablesFunction(): void
    {
        $this->emitter->on('foo', '\Evenement\Tests\setGlobalTestData');

        $this->emitter->emit('foo', ['bar']);

        self::assertSame('bar', $GLOBALS['evenement-evenement-test-data']);

        unset($GLOBALS['evenement-evenement-test-data']);
    }

    public function testListeners(): void
    {
        $onA = static function (): void {};
        $onB = static function (): void {};
        $onC = static function (): void {};
        $onceA = static function (): void {};
        $onceB = static function (): void {};
        $onceC = static function (): void {};

        self::assertCount(0, $this->emitter->listeners('event'));
        $this->emitter->on('event', $onA);
        self::assertCount(1, $this->emitter->listeners('event'));
        self::assertSame([$onA], $this->emitter->listeners('event'));
        $this->emitter->once('event', $onceA);
        self::assertCount(2, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onceA], $this->emitter->listeners('event'));
        $this->emitter->once('event', $onceB);
        self::assertCount(3, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onceA, $onceB], $this->emitter->listeners('event'));
        $this->emitter->on('event', $onB);
        self::assertCount(4, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onB, $onceA, $onceB], $this->emitter->listeners('event'));
        $this->emitter->removeListener('event', $onceA);
        self::assertCount(3, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onB, $onceB], $this->emitter->listeners('event'));
        $this->emitter->once('event', $onceC);
        self::assertCount(4, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onB, $onceB, $onceC], $this->emitter->listeners('event'));
        $this->emitter->on('event', $onC);
        self::assertCount(5, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onB, $onC, $onceB, $onceC], $this->emitter->listeners('event'));
        $this->emitter->once('event', $onceA);
        self::assertCount(6, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onB, $onC, $onceB, $onceC, $onceA], $this->emitter->listeners('event'));
        $this->emitter->removeListener('event', $onB);
        self::assertCount(5, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onC, $onceB, $onceC, $onceA], $this->emitter->listeners('event'));
        $this->emitter->emit('event');
        self::assertCount(2, $this->emitter->listeners('event'));
        self::assertSame([$onA, $onC], $this->emitter->listeners('event'));
        $this->emitter->removeAllListeners();
        $this->emitter->once('event', $onceA);
        self::assertCount(1, $this->emitter->listeners('event'));
        $this->emitter->removeListener('event', $onceA);
        self::assertCount(0, $this->emitter->listeners('event'));
    }

    public function testOnceCallIsNotRemovedWhenWorkingOverOnceListeners(): void
    {
        $aCalled = false;
        $aCallable = static function () use (&$aCalled): void {
            $aCalled = true;
        };
        $bCalled = false;
        $bCallable = function () use (&$bCalled, $aCallable): void {
            $bCalled = true;
            $this->emitter->once('event', $aCallable);
        };
        $this->emitter->once('event', $bCallable);

        self::assertFalse($aCalled);
        self::assertFalse($bCalled);
        $this->emitter->emit('event');

        self::assertFalse($aCalled);
        self::assertTrue($bCalled);
        $this->emitter->emit('event');

        self::assertTrue($aCalled);
        self::assertTrue($bCalled);
    }

    public function testEventNameMustBeStringOn(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('event name must not be an empty string');

        $this->emitter->on('', static function (): void {});
    }

    public function testEventNameMustBeStringOnce(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('event name must not be an empty string');

        $this->emitter->once('', static function (): void {});
    }

    public function testEventNameMustBeStringRemoveListener(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('event name must not be an empty string');

        $this->emitter->removeListener('', static function (): void {});
    }

    public function testEventNameMustBeStringEmit(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('event name must not be an empty string');

        $this->emitter->emit('');
    }

    public function testListenersGetAll(): void
    {
        $a = static function (): void {};
        $b = static function (): void {};
        $c = static function (): void {};
        $d = static function (): void {};

        $this->emitter->once('event2', $c);
        $this->emitter->on('event', $a);
        $this->emitter->once('event', $b);
        $this->emitter->on('event', $c);
        $this->emitter->once('event', $d);

        self::assertSame(
            [
                'event' => [
                    $a,
                    $c,
                    $b,
                    $d,
                ],
                'event2' => [
                    $c,
                ],
            ],
            $this->emitter->listeners()
        );
    }

    public function testOnceNestedCallRegression(): void
    {
        $first = 0;
        $second = 0;

        $this->emitter->once('event', function () use (&$first, &$second): void {
            $first++;
            $this->emitter->once('event', static function () use (&$second): void {
                $second++;
            });
            $this->emitter->emit('event');
        });
        $this->emitter->emit('event');

        self::assertSame(1, $first);
        self::assertSame(1, $second);
    }

    public function testNestedOn(): void
    {
        $emitter = $this->emitter;

        $first = 0;
        $second = 0;
        $third = 0;

        $emitter->on('event', static function () use (&$emitter, &$first, &$second, &$third): void {
            $first++;

            $emitter->on('event', static function () use (&$second, &$third): void {
                $second++;
            })
                ->once('event', static function () use (&$third): void {
                    $third++;
                });
        });

        $emitter->emit('event');
        self::assertSame(1, $first);
        self::assertSame(0, $second);
        self::assertSame(0, $third);
        $emitter->emit('event');
        self::assertSame(2, $first);
        self::assertSame(1, $second);
        self::assertSame(1, $third);
    }
}
