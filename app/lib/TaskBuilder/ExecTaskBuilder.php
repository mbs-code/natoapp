<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Events\DebugTaskEventer;
use App\Lib\TaskBuilder\Events\DebugBlackTaskEventer;
use Illuminate\Support\Collection;

class ExecTaskBuilder extends TaskBuilder
{
    protected bool $doColorDump = false; // カラーダンプモード
    protected bool $doDump = false; // ログダンプモード

    public static function run($value)
    {
        $inst = static::builder();
        $res = $inst->exec($value);
        return $res;
    }

    public function exec($value)
    {
        $eventer = $this->generateTaskEventer();
        $res = $this->task->exec($value, $eventer);
        return $res;
    }

    ///

    protected function generateTaskEventer(): TaskEventer
    {
        if ($this->doColorDump) {
            return new DebugTaskEventer();
        } else if ($this->doDump) {
            return new DebugBlackTaskEventer();
        }
        return new TaskEventer();
    }

    ///
    // extend builder

    public function colorDump() {
        $this->doColorDump = true;
        return $this;
    }

    public function dump() {
        $this->doDump = true;
        return $this;
    }

    ///
    // general tasks

    protected function collect()
    {
        return function($val) {
            return new Collection($val);
        };
    }

    protected function chunk(int $size)
    {
        return function(Collection $val) use ($size) {
            return $val->chunk($size);
        };
    }

    protected function flatten(int $depth = 1)
    {
        return function(Collection $val) use ($depth) {
            return $val->flatten($depth);
        };
    }
}
