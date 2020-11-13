<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\Traits\LoopLevelTrait;
use App\Lib\TaskBuilder\Events\Traits\TaskJobTrait;
use App\Lib\TaskBuilder\Utils\EventRecord;
use App\Lib\TaskBuilder\Utils\EventManager;

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
    // event 系

    public function fireEvent(string $shortKey, $value, callable $defaultFireFunc = null)
    {
        // イベント実行回数を記録
        $eventName = $this->record->incrementEventValue($shortKey, $this->getJobName());

        // イベント呼び出し
        $manager = $this->manager;
        if ($manager) {
            $manager->callEvent($eventName, $value, $this->record, $defaultFireFunc);
        }
        return $eventName;
    }

    ///
    // record 系

    public function write(string $name, $value)
    {
        // 生値を記録
        $this->record->put($name, $value);
        return $name;
    }

    public function writeRecord(string $shortKey, $value)
    {
        // レコードを記録
        $eventName = $this->record->setRecordValue($shortKey, $this->getJobName(), $value);
        return $eventName;
    }

    public function incrementRecord(string $shortKey)
    {
        // レコードを記録
        $eventName = $this->record->incrementRecordValue($shortKey, $this->getJobname());
        return $eventName;
    }

    public function clearRecords($shortKeys)
    {
        // レコードを削除
        $eventNames = collect($shortKeys)
            ->map(function ($shortKey) {
                return $this->record->clearRecordValue($shortKey, $this->getJobName());
            });

        return $eventNames;
    }
}
