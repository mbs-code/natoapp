<?php

namespace App\Lib\TaskBuilder\Jobs;

use App\Lib\TaskBuilder\Events\TaskEventer;
use Iterator;

class WhileLoopJob extends LoopJob
{
    protected $goNextFunc; // 次の要素へ進むか判定する関数

    function __construct(string $name, callable $func, callable $goNextFunc)
    {
        parent::__construct($name, $func);
        $this->goNextFunc = $goNextFunc;
    }

    protected function canNext(TaskEventer $e, Iterator $it)
    {
        // function (TaskEventer $e, Iterator $it): bool
        $val = call_user_func($this->goNextFunc, $e, $it);
        return $val;
    }
}
