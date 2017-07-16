<?php

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement\Tests;

use Evenement\EventLinkEmitter;

class EventLinkEmitterTest extends \PHPUnit_Framework_TestCase
{
    function testE2(){
        $dispacher = new EventLinkEmitter();
        $name = 'huo';
        $dispacher->on('user.create',function($param){
            return 'hello';
        });
        $dispacher->on('user.update',function($param){
            return ' ';
        });
        $dispacher->on('user.create',function($param){
            return 'world';
        });
        $data = $dispacher->emitByPrefix('user',[
            ['name'=>'liuc']
        ],EventLinkEmitter::MODE_APPEND);
        $this->assertArrayHasKey('user',$data);
        $this->assertArrayHasKey('create',$data['user']);
        if (is_array($data['user']['create'])) {
            $implode = implode(' ', $data['user']['create']);
        } else {
            $implode = '';
        }
        $join_string = $implode;
        $this->assertSame('hello world',$join_string);

        $this->assertArrayHasKey('update',$data['user']);
        $data = $dispacher->emitByPrefix('user',[
            ['name'=>'liuc']
        ],EventLinkEmitter::MODE_AS_KEY);
        $this->assertSame('world',$data['user']['create']);
        $this->assertSame(' ',$data['user']['update']);

    }
}
