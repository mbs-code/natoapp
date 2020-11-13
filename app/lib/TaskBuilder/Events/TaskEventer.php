<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\Traits\LoopLevelTrait;
use App\Lib\TaskBuilder\Events\Traits\TaskJobTrait;
use App\Lib\TaskBuilder\Utils\EventRecord;
use App\Lib\TaskBuilder\Utils\EventManager;
use Illuminate\Support\Str;

class TaskEventer
{
    use TaskJobTrait;
    use LoopLevelTrait;

    protected EventRecord $record; // イベントの実行回数等の記録
    protected EventManager $manager; // イベント管理

    function __construct()
    {
        $this->record = new EventRecord();
    }

    public function setEventManager(EventManager $manager)
    {
        $this->manager = $manager;
    }

    public function getEventManager()
    {
        return $this->manager;
    }

    ///

    public function fireEvent(string $shortKey, $value, callable $defaultFireFunc = null)
    {
        // event name を生成 (eventKey taskName の順序)
        $eventName = $this->generateEventName($shortKey, $this->getJobName(), 1);

        // イベント実行回数を記録
        $this->record->increment($eventName);

        // イベント呼び出し
        $manager = $this->manager;
        if ($manager) {
            $manager->callEvent($eventName, $value, $this->record, $defaultFireFunc);
        }
        return $eventName;
    }

    ///

    public function incrementRecord(string $shortKey)
    {
        // event name を生成 (taskName eventKey の順序)
        $eventName = $this->generateEventName($shortKey, $this->getJobName(), 0);

        // レコードを記録
        $this->record->increment($eventName);

        return $eventName;
    }

    public function writeRecord(string $shortKey, $value)
    {
        // event name を生成 (taskName eventKey の順序)
        $eventName = $this->generateEventName($shortKey, $this->getJobName(), 0);

        // レコードを記録
        $this->record->put($eventName, $value);

        return $eventName;
    }

    public function clearRecords($shortKey)
    {
        // event name を生成　(taskName eventKey の順序)
        $eventNames = collect($shortKey)
            ->map(fn($key) => $this->generateEventName($key, $this->getJobName(), 0));

        // レコードを削除
        foreach ($eventNames as $eventName) {
            $this->record->unset($eventName);
        }

        return $eventNames;
    }

    ///

    protected function generateEventName(string $shortKey, string $jobName = null, int $taskNameIndex = 1)
    {
        // ジョブがある時は結合する
        if ($jobName) {
            // スペース区切り
            $nameArray = Str::of($shortKey)->explode(' ');

            // name を挿入する
            $nameArray->splice($taskNameIndex, 0, $jobName);

            // snake case にして camel にする
            $snake = $nameArray->implode('_');
        } else {
            $snake = $shortKey;
        }

        $camel = Str::camel($snake);
        return $camel;
    }
}
