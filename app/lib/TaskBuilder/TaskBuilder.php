<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\Task;
use App\Lib\TaskBuilder\Utils\EventManager;
use App\Lib\TaskBuilder\Jobs\ProcessJob;
use App\Lib\TaskBuilder\Jobs\LoopJob;
use App\Lib\TaskBuilder\Jobs\MappingProcessJob;
use App\Lib\TaskBuilder\Jobs\WhileLoopJob;

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

    ///

    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        // builder の初期値を設定
        // please override
        return $builder;
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

    public function whileLoop(string $name, callable $func, callable $goNextFunc)
    {
        $job = new WhileLoopJob($name, $func, $goNextFunc);
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
}
