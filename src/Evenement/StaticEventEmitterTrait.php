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

trait StaticEventEmitterTrait
{
    private static $emitter;

    public static function emitter() {
        if (!isset(self::$emitter)) {
            self::$emitter = new EventEmitter();
        }
        return self::$emitter;
    }

    public static function __callStatic($name, $args)
    {
        $emitter = self::emitter();

        if (method_exists($emitter, $name)) {
            return call_user_func_array([$emitter, $name], $args);
        }

        // Produce a fatal error if method does not exist
        $class = static::class;
        trigger_error("Call to undefined method $class::$name()", E_USER_ERROR);
    }
}
