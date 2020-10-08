<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\Task;

abstract class UpsertTask extends Task
{
    protected abstract function fetch($var);

    // protected abstract function handle($data);

    protected function preFormat($var)
    {
        return $var;
    }

    /// ////////////////////////////////////////

    // @override
    public static function run($var)
    {
        $instance = self::getInstance();
        $res = $instance->exec($var);
        return $res;
    }

    // @override
    public function exec($var)
    {
        // 引数の事前整形
        $formatVar = $this->preFormat($var);

        // fetch 実行
        $fetch = $this->fetch($formatVar);

        // process 実行
        $buffer = $this->process($fetch);

        return $buffer;
    }

    protected function process($data)
    {
        $res = $this->handle($data);
        return $res;
    }
}
