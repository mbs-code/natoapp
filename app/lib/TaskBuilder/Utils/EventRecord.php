<?php

namespace App\Lib\TaskBuilder\Utils;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EventRecord extends Collection
{
    ///
    // extend

    public function increment($key)
    {
        $num = $this->get($key, 0) + 1;
        $this->put($key, $num);
        return $num;
    }

    public function unset($key)
    {
        return $this->pull($key);
    }

    ///
    // 追加拡張

    public function setEventValue(string $shortKey, string $jobName = null, $value)
    {
        $key = static::generateEventName($shortKey, $jobName);
        $this->put($key, $value);
        return $key; // 追加キーを返却
    }

    public function setRecordValue(string $shortKey, string $jobName = null, $value)
    {
        $key = static::generateRecordName($shortKey, $jobName);
        $this->put($key, $value);
        return $key; // 追加キーを返却
    }

    public function incrementEventValue(string $shortKey, string $jobName = null)
    {
        $key = static::generateEventName($shortKey, $jobName);
        $this->increment($key);
        return $key; // 追加キーを返却
    }

    public function incrementRecordValue(string $shortKey, string $jobName = null)
    {
        $key = static::generateRecordName($shortKey, $jobName);
        $this->increment($key);
        return $key; // 追加キーを返却
    }

    public function clearRecordValue(string $shortKey, string $jobName = null)
    {
        $key = static::generateRecordName($shortKey, $jobName);
        $val = $this->unset($key);
        return $key; // 削除キーを返却
    }

    public function getEventValue(string $shortKey, string $jobName = null, $default = null)
    {
        $key = static::generateEventName($shortKey, $jobName);
        return $this->get($key, $default);
    }

    public function getRecordValue(string $shortKey, string $jobName = null, $default = null)
    {
        $key = static::generateRecordName($shortKey, $jobName);
        return $this->get($key, $default);
    }

    ///
    // key 名生成関数

    public static function generateEventName(string $shortKey, string $jobName = null)
    {
        return static::generateName($shortKey, $jobName, 1);
    }

    public static function generateRecordName(string $shortKey, string $jobName = null)
    {
        return static::generateName($shortKey, $jobName, 0);
    }

    protected static function generateName(string $shortKey, string $jobName = null, int $jobNameIndex = 1)
    {
        // TODO: 需要があれば外部から弄れるように
        // ジョブがある時は結合する
        if ($jobName) {
            // スペース区切り
            $nameArray = Str::of($shortKey)->explode(' ');

            // name を挿入する
            $nameArray->splice($jobNameIndex, 0, $jobName);

            // snake case にして camel にする
            $snake = $nameArray->implode('_');
        } else {
            $snake = $shortKey;
        }

        $camel = Str::camel($snake);
        return $camel;
    }
}
