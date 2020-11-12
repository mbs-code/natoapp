<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\Attrs\ProcessAttr;
use App\Lib\TaskBuilder\Attrs\LoopAttr;
use App\Lib\TaskBuilder\Attrs\MappingProcessAttr;
use App\Lib\TaskBuilder\Events\TaskEventer;
use Illuminate\Support\Collection;
use LogicException;

class TaskBuilder
{
    private const RESERVE_WORDS = [
        'before', 'after', 'loop', 'length', 'key', // base event
        'current', 'success', 'skip', 'throw', 'exception', // stats
        'mapping', // other
    ];

    private $flow; // task flow

    function __construct()
    {
        $this->flow = new Collection();
    }

    public static function builder()
    {
        return new static();
    }

    ///

    public function exec($value, TaskEventer $e = null)
    {
        // eventer の作成 or 引き継ぎ
        $e = $e ?? new TaskEventer();

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

    public function process(string $name, callable $func, bool $intoArray = false)
    {
        $this->checkTaskName($name);

        $attr = new ProcessAttr($name, $func, $intoArray);
        $this->flow->push($attr);
        return $this;
    }

    public function mappingProcess(string $name, callable $func, callable $keyOfItemFunc, bool $intoArray = false)
    {
        // 継承の関係上、引数の位置が異なる
        $this->checkTaskName($name);

        $attr = new MappingProcessAttr($name, $func, $intoArray, $keyOfItemFunc);
        $this->flow->push($attr);
        return $this;
    }

    public function loop(string $name, callable $func)
    {
        $this->checkTaskName($name);

        $attr = new LoopAttr($name, $func);
        $this->flow->push($attr);
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
