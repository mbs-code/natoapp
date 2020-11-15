<?php

namespace App\Lib\TaskBuilder\Jobs;

use App\Lib\TaskBuilder\Events\TaskEventer;

class MappingProcessJob extends ProcessJob
{
    protected $keyOfItemFunc; // マップ用関数

    function __construct(string $name, callable $func, bool $intoArray, callable $keyOfItemFunc)
    {
        parent::__construct($name, $func, $intoArray);
        $this->keyOfItemFunc = $keyOfItemFunc;
    }

    public function handle($value, TaskEventer $e, $arg = null)
    {
        $res = parent::handle($value, $e, $arg);

        // key に対して value を結びつける
        $mapRes = $this->mapping($value, $res);

        // 後処理
        $e->fireEvent('after mapping', $mapRes);

        return $mapRes;
    }

    private function mapping(iterable $args, iterable $items)
    {
        // 引数値を配列にする (null key map)
        $argMap = collect($args)->mapWithKeys(function ($key) {
            return [$key => null];
        });

        // 一致するキーに map を配置していく
        foreach ($items as $item) {
            // TODO: try catch
            // function ($item, $argMap): mixed
            $key = call_user_func($this->keyOfItemFunc, $item, $argMap);
            $argMap->put($key, $item);
        }

        return $argMap;
    }
}
