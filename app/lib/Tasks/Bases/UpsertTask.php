<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\Task;

abstract class UpsertTask extends Task
{
    protected abstract function fetch( $var);

    protected abstract function process($data);

    /// ////////////////////////////////////////

    public static function run($var)
    {
        $instance = self::getInstance();
        $res = $instance->exec($var);
        return $res;
    }

    public function exec($var)
    {
        // fetch 実行
        $fetch = $this->fetch($var);
        $isArray = is_array($fetch); // fetch の戻り値検査

        $ary = $isArray ? $fetch : [$fetch];
        $len = count($ary);

        $buffer = collect();
        foreach ($ary as $key => $data) {
            // process 実行
            $res = $this->process($data);
            $buffer->push($res);
            $this->fireEvent('inserted', $res, $key, $len);
        }

        // 配列モードなら配列、単体なら単体を返却
        return $isArray ? $buffer : $buffer->first();
    }
}
