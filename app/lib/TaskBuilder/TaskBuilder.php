<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\Jobs\ProcessJob;
use App\Lib\TaskBuilder\Jobs\LoopJob;
use App\Lib\TaskBuilder\Jobs\MappingProcessJob;
use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Utils\EventManager;
use App\Lib\TaskBuilder\Utils\TaskFlow;
use LogicException;

class TaskBuilder
{
    private const RESERVE_WORDS = [
        'before', 'after', 'task', 'loop', 'length', 'key', // base event
        'index', 'current', 'success', 'skip', 'throw', 'exception', // stats
        'mapping', // other
    ];

    private TaskFlow $flow;
    private EventManager $manager;

    function __construct(EventManager $manager = null)
    {
        $this->flow = new TaskFlow();
        $this->manager = $manager ?? new EventManager();
        // $manager = null なら親要素
    }

    public static function builder(EventManager $manager = null)
    {
        return new static($manager);
    }

    ///

    public function exec($value, TaskEventer $e = null)
    {
        // eventer の作成 or 引き継ぎ => manager を付与
        $e = $e ?? new TaskEventer();
        $e->setEventManager($this->manager);

        $e->fireEvent('before task', $value);

        // Task 実行
        $res = $this->handle($value, $e);

        $e->fireEvent('after task', $res);

        return $res;
    }

    public function handle($value, TaskEventer $e)
    {
        // !!! lib内部実行用 (Eventer を引き継ぐ)
        // iterator の取得
        $it = $this->flow->getIterator();
        $it->rewind();

        // flow を順番に実行する
        $buffer = $value; // コンテナ (置換)
        while ($it->valid()) {
            $task = $it->current();

            // タスクの実行
            $res = $task->call($e, $buffer);
            $buffer = $res;

            // 次へ
            $it->next();
        }

        return $buffer;
    }

    ///
    // event 系

    public function silent(bool $isMute = true)
    {
        $this->manager->isMute($isMute);
        return $this;
    }

    public function addEvent(string $name, callable $fireFunc)
    {
        $this->manager->addEvent($name, $fireFunc);
        return $this;
    }

    public function addEvents(iterable $events)
    {
        foreach ($events as $name => $func) {
            // 単体でも配列でも処理可能
            $fireFuncs = is_iterable($func) ? $func : [$func];
            foreach ($fireFuncs as $fireFunc) {
                $this->manager->addEvent($name, $fireFunc);
            }
        }
        return $this;
    }

    ///
    // job 系

    public function process(string $name, callable $func, bool $intoArray = false)
    {
        $this->checkTaskName($name);

        $job = new ProcessJob($name, $func, $intoArray);
        $this->flow->push($job);
        return $this;
    }

    public function mappingProcess(string $name, callable $func, callable $keyOfItemFunc, bool $intoArray = false)
    {
        // 継承の関係上、引数の位置が異なる
        $this->checkTaskName($name);

        $job = new MappingProcessJob($name, $func, $intoArray, $keyOfItemFunc);
        $this->flow->push($job);
        return $this;
    }

    public function loop(string $name, callable $func)
    {
        $this->checkTaskName($name);

        $job = new LoopJob($name, $func);
        $this->flow->push($job);
        return $this;
    }

    ///

    private function checkTaskName(string $name)
    {
        // 予約語をチェック
        if (in_array($name, self::RESERVE_WORDS)) {
            throw new LogicException("Task name is reserved: {$name}");
        }
    }
}
