<?php

namespace App\Lib\Tasks;

use App\Lib\SingletonTrait;

abstract class ChunkUpsertTask
{
    use SingletonTrait;

    protected $itemLengthOnce = 10;

    protected abstract function fetch(array $items);

    protected abstract function parse(object $item);

    /// ////////////////////////////////////////

    public static function run(string $item)
    {
        $res = self::runs([$item]);
        return data_get($res, '0');
    }

    public static function runs(array $items)
    {
        $instance = self::getInstance();
        $res = $instance->handle($items);
        return $res;
    }

    public function handle(array $items)
    {
        $chunks = collect($items)->chunk($this->itemLengthOnce);

        // chunk ごとに実行
        $buffer = collect();
        foreach ($chunks as $chunk) {
            $res = $this->chunkHandle($chunk->toArray());
            $buffer->push($res);
        }
        return $buffer->flatten();
    }

    protected function chunkHandle(array $items)
    {
        // fetch 実行
        $fetch = $this->fetch($items);

        $buffer = collect();
        foreach ($fetch as $data) {
            // parse 実行
            $res = $this->parse($data);
            $buffer->push($res);
        }
        return $buffer;
    }
}
