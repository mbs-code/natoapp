<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\Task;
use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Utils\EventManager;
use App\Lib\TaskBuilder\Jobs\ProcessJob;
use App\Lib\TaskBuilder\Jobs\LoopJob;
use App\Lib\TaskBuilder\Jobs\MappingProcessJob;
use Illuminate\Support\Collection;

class TaskBuilder
{
    protected Task $task;

    protected function __construct(EventManager $manager = null)
    {
        // 親要素(root) は $manager = null、子要素は引き継ぐ
        $this->task = new Task($manager);
    }

    public static function builder(EventManager $manager = null)
    {
        // 親要素(root) は $manager = null、子要素は引き継ぐ
        $inst = new static($manager);
        $inst->generateTaskflow($inst); // デフォルトタスク
        return $inst;
    }

    public static function run($value, TaskEventer $eventer = null)
    {
        $inst = static::builder();
        $res = $inst->exec($value, $eventer);
        return $res;
    }

    ///

    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        // builder の初期値を設定
        // please override
        return $builder;
    }

    public function exec($value, TaskEventer $eventer = null)
    {
        $res = $this->task->exec($value, $eventer);
        return $res;
    }

    // key は loop のキーとかを入れる用
    // !!! lib内部実行用 (Eventer を引き継ぐ)
    public function handle($value, TaskEventer $eventer, $key = null)
    {
        $res = $this->task->handle($value, $eventer, $key);
        return $res;
    }

    ///
    // job 系

    public function process(string $name, callable $func, bool $intoArray = false)
    {
        $job = new ProcessJob($name, $func, $intoArray);
        $this->task->addJob($job);
        return $this;
    }

    public function mappingProcess(string $name, callable $func, callable $keyOfItemFunc, bool $intoArray = false)
    {
        // 継承の関係上、引数の位置が異なる
        $job = new MappingProcessJob($name, $func, $intoArray, $keyOfItemFunc);
        $this->task->addJob($job);
        return $this;
    }

    public function loop(string $name, callable $func)
    {
        $job = new LoopJob($name, $func);
        $this->task->addJob($job);
        return $this;
    }

    ///
    // event 系

    public function addEvent(string $name, callable $fireFunc)
    {
        $this->task->addEvent($name, $fireFunc);
        return $this;
    }

    public function addEvents(iterable $events)
    {
        // ['eventName' => function(){} | [function(){}... ]]
        foreach ($events as $name => $func) {
            // 単体でも配列でも処理可能
            $fireFuncs = is_iterable($func) ? $func : [$func];
            foreach ($fireFuncs as $fireFunc) {
                $this->task->addEvent($name, $fireFunc);
            }
        }
        return $this;
    }

    public function silent(bool $isMute = true)
    {
        $this->task->isMute($isMute);
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
