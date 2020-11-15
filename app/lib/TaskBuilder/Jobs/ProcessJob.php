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
        // 前処理 (引数を collection にする)
        $items = $this->intoArray && !($value instanceof Collection)
            ? collect($value)
            : $value;
        $e->fireEvent('before', $items);

        // タスクの実行
        // TODO: try catch
        // function (mixed $items, TaskEventer $e, mixed $arg = null): mixed
        // key は loop 時に使える
        $res = call_user_func($this->func, $items, $e, $arg);

        // 後処理
        $e->fireEvent('after', $res);

        return $res;
    }
}
