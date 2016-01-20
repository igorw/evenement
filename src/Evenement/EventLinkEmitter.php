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

class EventLinkEmitter implements EventEmitterInterface
{
    CONST MODE_APPEND = 1;
    CONST MODE_AS_KEY = 2;
    use EventEmitterTrait;

    public function emitByPrefix($eventPrefix, array $arguments = [],$mode=self::MODE_APPEND)
    {
        $result = [];
        $keys = (array)array_keys($this->listeners);
        foreach ($keys as $key) {
            if (strpos($key,$eventPrefix) === 0) {

                foreach ($this->listeners($key) as $listener) {
                    $ev_info = explode('.',$key);
                    if (count($ev_info) > 1) {
                        $ev_sub = $ev_info[1];
                        $ev_first = $ev_info[0];
                        $call_user_func_array = call_user_func_array($listener, $arguments);
                        if ($mode == self::MODE_APPEND) {
                            $result[$ev_first][$ev_sub][] = $call_user_func_array;
                        }else{
                            $result[$ev_first][$ev_sub] = $call_user_func_array;
                        }
                    }else{
                        $call_user_func_array = call_user_func_array($listener, $arguments);
                        if ($mode == self::MODE_APPEND) {
                            $result[$key][] = $call_user_func_array;
                        }else{
                            $result[$key] = $call_user_func_array;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
