<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\FetchArrayTask;

abstract class ChunkFetchArrayTask extends FetchArrayTask
{
    protected $chunkSize = 10; // 1チャンクのサイズ

    protected function preFormat($var)
    {
        return collect($var)
            ->chunk($this->chunkSize);
    }
}
