<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\UpsertTask;

abstract class ArrayUpsertTask extends UpsertTask
{
    // @override
    protected function process($data)
    {
        $isArray = is_array($data); // 引数の配列チェック
        $ary = $isArray ? $data : [$data];
        $len = count($ary);

        $buffer = collect();
        foreach ($ary as $key => $data) {
            // process 実行
            $res = $this->handle($data);
            $buffer->push($res);
            $this->fireEvent('inserted', $res, $key, $len);
        }

        // 配列モードなら配列、単体なら単体を返却
        return $isArray ? $buffer : $buffer->first();
    }
}
