<?php

namespace App\Lib\TaskBuilder\Jobs;

use App\Lib\TaskBuilder\Events\TaskEventer;

abstract class BaseJob
{
    protected $name; // 名前
    protected $func; // タスク関数

    function __construct(string $name, callable $func)
    {
        $this->name = $name;
        $this->func = $func;
    }

    public abstract function handle(TaskEventer $e, $value);

    public function getName()
    {
        return $this->name;
    }

    public function call(TaskEventer $e, $value)
    {
        // eventer に自身の job を付与
        $e->pushEventJob($this);

        // task 実行
        $res = null;
        try {
            $res = $this->handle($e, $value);
        } finally {
            // eventer から job を取り出す (必ず)
            $e->popEventJob($this);
        }

        return $res;
    }
}
