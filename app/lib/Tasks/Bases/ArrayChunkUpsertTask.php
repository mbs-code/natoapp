<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\ArrayUpsertTask;

abstract class ArrayChunkUpsertTask extends ArrayUpsertTask
{
    protected $chunkSize = 10; // 1チャンクのサイズ

    public static function runs(array $items)
    {
        $instance = self::getInstance();
        $res = $instance->execArray($items);
        return $res;
    }

    /// ////////////////////////////////////////

    // @override
    public function exec($var)
    {
        // 単体実行
        $ary = is_array($var) ? $var : [$var];
        $res = parent::exec($ary);
        return $res->first();
    }

    public function execArray(array $items)
    {
        $chunks = collect($items)->chunk($this->chunkSize)->toArray();

        // chunk ごとに実行
        $buffer = collect();
        foreach ($chunks as $chunk) {
            $res = parent::exec($chunk);
            $buffer->push($res);
        }
        return $buffer->flatten();
    }
}
