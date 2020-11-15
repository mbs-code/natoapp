<?php

namespace App\Lib\TaskBuilder\Jobs;

use App\Lib\TaskBuilder\Events\TaskEventer;
use Illuminate\Support\Collection;

class ProcessJob extends BaseJob
{
    protected $intoArray; // 引数を強制的に配列にするか

    function __construct(string $name, callable $func, bool $intoArray)
    {
        parent::__construct($name, $func);
        $this->intoArray = $intoArray;
    }

    public function handle($value, TaskEventer $e, $arg = null)
    {
        // 引数を collection にする
        $items = $this->intoArray && !($value instanceof Collection)
            ? collect($value)
            : $value;

        // 前処理
        $key = is_countable($items) ? count($items) : $items;
        $e->writeRecord('key', $key); // key に長さか値を突っ込んどく
        $e->fireEvent('before', $items);

        // タスクの実行
        // TODO: try catch
        // function (mixed $items, TaskEventer $e, mixed $arg = null): mixed
        // key は loop 時に使える
        $res = call_user_func($this->func, $items, $e, $arg);

        // 後処理
        $e->fireEvent('after', $res);
        $e->clearRecords('key');

        return $res;
    }
}
