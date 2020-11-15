<?php

namespace App\Lib\TaskBuilder\Jobs;

use App\Lib\TaskBuilder\Events\TaskEventer;

abstract class BaseJob
{
    private const RESERVE_WORDS = [
        'before', 'after', 'task', 'loop', 'length', 'key', // base event
        'index', 'current', 'success', 'skip', 'throw', 'exception', // stats
        'mapping', // other
    ];

    protected $name; // 名前
    protected $func; // タスク関数

    function __construct(string $name, callable $func)
    {
        // 予約語をチェック
        if (in_array($name, self::RESERVE_WORDS)) {
            throw new LogicException("Job name is reserved: {$name}");
        }

        $this->name = $name;
        $this->func = $func;
    }

    public abstract function handle($value, TaskEventer $e, $arg = null);

    public function getName()
    {
        return $this->name;
    }

    public function call($value, TaskEventer $e, $arg = null)
    {
        // eventer に自身の job を付与
        $e->pushEventJob($this);

        // task 実行
        $res = null;
        try {
            $res = $this->handle($value, $e, $arg);
        } finally {
            // eventer から job を取り出す (必ず)
            $e->popEventJob($this);
        }

        return $res;
    }
}
