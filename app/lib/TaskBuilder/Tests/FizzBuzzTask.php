<?php

namespace App\Lib\TaskBuilder\Tests;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;

class FizzBuzzTask extends ExecTaskBuilder
{
    protected function taskFlow(TaskBuilder $builder): TaskBuilder
    {
        // 配列を5つずつの塊にして、0以下は skip, 3の倍数は fizz, 5の倍数は buzz にする
        return $builder
            ->process('chunk', $this->chunk(5), true)
            ->loop('outer', function ($b) {
                $b->loop('inner', function ($bb) {
                    $bb->process('judge', $this->fizzBuzz());
                });
            })
            ->process('flatten', $this->flatten());
    }

    ///

    private function fizzBuzz()
    {
        return function($val) {
            if ($val <= 0) return false;

            if ($val % 15 === 0) {
                return 'fizzBuzz';
            } else if ($val % 3 === 0) {
                return 'fizz';
            } else if ($val % 5 === 0) {
                return 'buzz';
            }
            return $val;
        };
    }
}
