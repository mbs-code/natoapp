<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\Traits\LoopLevelTrait;
use App\Lib\TaskBuilder\Events\Traits\TaskAttrTrait;
use App\Lib\TaskBuilder\Events\Traits\CallEventTrait;
use App\Lib\TaskBuilder\Utils\EventRecord;
use Illuminate\Support\Str;

class TaskEventer
{
    use TaskAttrTrait;
    use CallEventTrait;
    use LoopLevelTrait;

    protected $record; // イベントの実行回数等の記録

    function __construct()
    {
        $this->record = new EventRecord();
    }

    ///

    public function fireEvent(string $shortKey, $value)
    {
        // event name を生成 (eventKey taskName の順序)
        $eventName = $this->generateEventName($shortKey, $this->getTaskName(), 1);

        // イベント実行回数を記録
        $this->record->increment($eventName);

        // イベント呼び出し
        $this->callEvent($eventName, $value, $this->record);
        return $eventName;
    }

    ///

    public function incrementRecord(string $shortKey)
    {
        // event name を生成 (taskName eventKey の順序)
        $eventName = $this->generateEventName($shortKey, $this->getTaskName(), 0);

        // レコードを記録
        $this->record->increment($eventName);

        return $eventName;
    }

    public function writeRecord(string $shortKey, $value)
    {
        // event name を生成 (taskName eventKey の順序)
        $eventName = $this->generateEventName($shortKey, $this->getTaskName(), 0);

        // レコードを記録
        $this->record->put($eventName, $value);

        return $eventName;
    }

    public function clearRecords($shortKey)
    {
        // event name を生成　(taskName eventKey の順序)
        $eventNames = collect($shortKey)
            ->map(fn($key) => $this->generateEventName($key, $this->getTaskName(), 0));

        // レコードを削除
        foreach ($eventNames as $eventName) {
            $this->record->unset($eventName);
        }

        return $eventNames;
    }

    ///

    protected function generateEventName(string $shortKey, string $taskName, int $taskNameIndex = 1)
    {
        // スペース区切り
        $nameArray = Str::of($shortKey)->explode(' ');

        // name を挿入する
        $nameArray->splice($taskNameIndex, 0, $taskName);

        // snake case にして camel にする
        $snake = $nameArray->implode('_');
        $camel = Str::camel($snake);
        return $camel;
    }
}
