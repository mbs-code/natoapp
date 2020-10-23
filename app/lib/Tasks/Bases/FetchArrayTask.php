<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\FetchTask;
use App\Lib\Tasks\Events\EventAttrs;
use Iterator;
use Exception;

/**
 * preFormat() -> out:[ fetch() -> in:[ handle() ] ] -> postFormat()
 * ※ fetch で scala 値を返却した場合、単体ループモードとなる.
 *
 * <event> attribute:
 * - <beforeOuterLoop>: outer loop 前処理
 * - <outerLooped>: outer loop 後処理
 *   - outerProps: 外部ループ前の値
 *   - outerScala: true で初期値がスカラ値かどうか (単体実行)
 *   - outerLength: 外部ループの長さ
 *
 * - <outerException>: 内部で例外を吐いた時
 *   - outerKey: 実行しているキー (意味ないかも)
 *   - outerIndex: 実行している外部ループの index
 *     - outerTotal: 実行回数統計
 *     - innerSuccess: 成功回数統計
 *     - innerSkip: 失敗回数統計
 *     - innerThrow: 例外回数統計
 *   - exception: 例外内容
 */
abstract class FetchArrayTask extends FetchTask
{
    protected function outerLoopCondition(Iterator $it)
    {
        return $it->valid();
    }

    protected function outerLoopNext(Iterator $it)
    {
        $it->next();
    }

    // @override
    protected function process($data, EventAttrs $e)
    {
        $e->outerScala = is_iterable($data);
        $items = collect($data);

        $e->outerProps = $items;
        $e->outerLength = $items->count();
        $e->outerIndex = 0;

        $e->outerTotal = 0;
        $e->outerSuccess = 0;
        $e->outerSkip = 0;
        $e->outerThrow = 0;

        // ■ start outer loop
        $this->fireEvent('beforeOuterLoop', $e);

        // 手動で iterator を回す
        $outer = collect();
        $it = $e->outerProps->getIterator();
        $it->rewind();

        // outer の統計は取得していない（全体を繰り返すという処理なので必要ない）
        while ($this->outerLoopCondition($it)) {
            $key = $it->key();
            $item = $it->current();

            try {
                $e->outerKey = $key;
                $e->outerIndex += 1;

                // parent process
                $outerRes = parent::process($item, $e);
                // outer props と response は inner の cache を使う

                $e->outerTotal += $e->innerIndex; // cache から処理数を読み取る
                $e->outerSuccess += $e->innerSuccess;
                $e->outerSkip += $e->innerSkip;
                $e->outerThrow += $e->innerThrow;

                $outer->push($outerRes);
            } catch (Exception $ex) {
                $e->exception = $ex;
                $this->fireEvent('outerException', $e, function () use ($ex) {
                    throw $ex; // event の指定が無いなら例外を投げる
                });
            }

            $this->outerLoopNext($it);
        }

        $e->outerResponse = $outer;
        $this->fireEvent('outerLooped', $e);
        // ■ end outer loop

        // scala mode なら単体を返却する
        return $e->outerScala ? $e->outerResponse->first() : $e->outerResponse;
    }
}
