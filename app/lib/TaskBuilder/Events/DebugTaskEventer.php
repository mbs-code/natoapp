<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\TaskEventer;

class DebugTaskEventer extends TaskEventer
{
    public $indent = '  ';

    public function fireEvent(string $key, $value)
    {
        $eventName = parent::fireEvent($key, $value);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getNestLevel());
        $count = $this->record->get($eventName);
        $val = $this->toJsonString($value);
        $this->dump("{$padding}[{$count}] <{$eventName}>: {$val}");
    }

    ///

    public function incrementRecord(string $key)
    {
        $eventName = parent::incrementRecord($key);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getNestLevel()); // スペース
        $count = $this->record->get($eventName);
        $this->dump("{$padding}=> {$eventName}: {$count}");

        return $eventName;
    }

    public function writeRecord(string $key, $value)
    {
        $eventName = parent::writeRecord($key, $value);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getNestLevel()); // スペース
        $val = $this->toJsonString($value);
        $this->dump("{$padding}=> {$eventName}: {$val}");

        return $eventName;
    }

    public function clearRecords($keys)
    {
        $eventNames = parent::clearRecords($keys);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getNestLevel()); // スペース
        $name = $this->toJsonString($eventNames);
        $this->dump("{$padding}!! {$name}");

        return $eventNames;
    }

    ///

    protected function dump($val)
    {
        echo($val.PHP_EOL);
    }

    protected function toJsonString($val)
    {
        return json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
