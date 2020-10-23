<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\Task;
use App\Lib\Tasks\Events\EventAttrs;
use Iterator;
use Exception;
use LogicException;

/**
 * preFormat() -> fetch() -> in:[ handle() ] -> postFormat()
 * - fetch で scala 値を返却した場合、単体ループモードとなる.
 * - 一つの値を handle() で処理したい場合、[$var] 形式で返却するべし.
 *
 * <event> attribute:
 * - <beforeFetch> fetch 前処理
 * - <fetched> fetch 後処理
 *   - fetchProps: fetch 前の値
 *   - fetchResponse: fetch 後の値
 *
 * - <beforeInnerLoop>: inner loop 前処理
 * - <innerLooped>: inner loop 後処理
 *   - innerProps: 内部ループ前の値
 *   - innerScala: true で初期値がスカラ値かどうか (単体実行)
 *   - innerLength: 内部ループの長さ
 *
 * - <innerSuccess>: 処理に成功した時
 * - <innerSkip>: スキップしたとき
 * - <innerException>: 例外を吐いたとき
 *   - innerKey: 実行しているキー
 *   - innerIndex: 実行している内部ループの loop index (1から, success + skip + throw)
 *     - innerSuccess: 成功した回数
 *     - innerSkip: 失敗した回数 (false で失敗扱い)
 *     - innerThrow: 例外実行回数
 *   - exception: 例外内容
 */
abstract class FetchTask extends Task
{
    protected $doMapping = false;

    protected abstract function fetch($var);

    protected function getKeyCallback($item, $keys) {
        throw new LogicException('Please override or $this->doMapping = false');
    }

    /// ////////////////////////////////////////

    protected function innerLoopCondition(Iterator $it)
    {
        return $it->valid();
    }

    protected function innerLoopNext(Iterator $it)
    {
        $it->next();
    }

    // @override
    protected function process($data, EventAttrs $e)
    {
        $e->fetchProps = $data;

        // ■ start fetch
        $this->fireEvent('beforeFetch', $e);

        $fetch = $this->fetch($e->fetchProps);
        $e->fetchResponse = $fetch;

        $this->fireEvent('fetched', $e);
        // ■ end fetch

        $e->innerScala = is_iterable($fetch);
        $map = $this->doMapping
            ? $this->mapping(collect($e->fetchProps), collect($e->fetchResponse)) // data に対して mapping する
            : $e->fetchResponse;
        $items = collect($map);

        $e->innerProps = $items;
        $e->innerLength = $items->count();
        $e->innerIndex = 0;

        $e->innerSuccess = 0;
        $e->innerSkip = 0;
        $e->innerThrow = 0;

        // ■ start inner loop
        $this->fireEvent('beforeInnerLoop', $e);

        // 手動で iterator を回す
        $inner = collect(); // 戻り値の箱
        $it = $e->innerProps->getIterator();
        $it->rewind();

        // false は失敗、null は例外、それ以外は成功
        while($this->innerLoopCondition($it)) {
            $key = $it->key();
            $item = $it->current();

            try {
                $e->innerKey = $key;
                $e->innerIndex += 1;

                // parent process
                $innerRes = parent::process($item, $e);
                // inner props と response は handle の cache を使う

                if ($innerRes === false) {
                    $e->innerSkip += 1;
                    $this->fireEvent('innerSkip', $e);
                } else {
                    $e->innerSuccess += 1;
                    $this->fireEvent('innerSuccess', $e);
                }

                $inner->push($innerRes);
            } catch (Exception $ex) {
                $e->innerThrow += 1;
                $e->exception = $ex;
                $this->fireEvent('innerException', $e, function () use ($ex) {
                    throw $ex; // event の指定が無いなら例外を投げる
                });
            }

            $this->innerLoopNext($it);
        }

        $e->innerResponse = $inner;
        $this->fireEvent('innerLooped', $e);
        // ■ end inner loop

        // scala mode なら単体を返却する
        return $e->innerScala ? $e->innerResponse->first() : $e->innerResponse;
    }

    private function mapping(iterable $keys, iterable $items)
    {
        // null map
        // key は単体でも配列にする
        $map = collect($keys)->mapWithKeys(function ($key) {
            return [$key => null];
        });

        // item mapping
        foreach ($items as $item) {
            $key = $this->getKeyCallback($item, $keys);
            $map->put($key, $item);
        }

        return $map;
    }
}
