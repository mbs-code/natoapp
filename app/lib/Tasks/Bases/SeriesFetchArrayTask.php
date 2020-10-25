<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Bases\FetchArrayTask;
use App\Lib\Tasks\Events\EventAttrs;
use Iterator;
use Exception;

/**
 * series:[preFormat() -> out:[ fetch() -> in:[ handle() ] ] -> postFormat()]
 * ※ fetch で scala 値を返却した場合、単体ループモードとなる.
 *
 * <event> attribute:
 * - <beforeSeriesLoop>: series loop 前処理
 * - <seriesLooped>: series loop 後処理
 *   - seriesProps: 直列ループ前の値
 *   - seriesScala: true で初期値がスカラ値かどうか (単体実行)
 *   - seriesLength: 直列ループの長さ
 *
 * - <seriesException>: 内部で例外を吐いた時
 *   - seriesKey: 実行しているキー (意味ないかも)
 *   - seriesIndex: 実行している直列ループの index
 *     - seriesTotal: 実行回数統計
 *     - seriesSuccess: 成功回数統計
 *     - seriesSkip: 失敗回数統計
 *     - seriesThrow: 例外回数統計
 *   - exception: 例外内容
 */
abstract class SeriesFetchArrayTask extends FetchArrayTask
{
    protected function seriesLoopCondition(Iterator $it)
    {
        return $it->valid();
    }

    protected function seriesLoopNext(Iterator $it)
    {
        $it->next();
    }

    // @override
    protected function wrapProcess($data, EventAttrs $e)
    {
        $e->seriesScala = is_iterable($data);
        $items = collect($data);

        $e->seriesProps = $items;
        $e->seriesLength = $items->count();
        $e->seriesIndex = 0;

        $e->seriesTotal = 0;
        $e->seriesSuccess = 0;
        $e->seriesSkip = 0;
        $e->seriesThrow = 0;

        // ■ start series loop
        $this->fireEvent('beforeSeriesLoop', $e);

        // 手動で iterator を回す
        $series = collect();
        $it = $e->seriesProps->getIterator();
        $it->rewind();

        // series loop
        while ($this->seriesLoopCondition($it)) {
            $key = $it->key();
            $item = $it->current();

            try {
                $e->seriesKey = $key;
                $e->seriesIndex += 1;

                // parent process
                $seriesRes = parent::wrapProcess($item, $e);
                // series props と response は outer の cache を使う

                $e->seriesTotal += $e->outerTotal;
                $e->seriesSuccess += $e->outerSuccess;
                $e->seriesSkip += $e->outerSkip;
                $e->seriesThrow += $e->outerThrow;

                $series->push($seriesRes);
            } catch (Exception $ex) {
                $e->exception = $ex;
                $this->fireEvent('seriesException', $e, function () use ($ex) {
                    throw $ex; // event の指定が無いなら例外を投げる
                });
            }

            $this->seriesLoopNext($it);
        }

        $e->seriesResponse = $series;
        $this->fireEvent('seriesLooped', $e);
        // ■ end series loop

        // scala mode なら単体を返却する
        return $e->seriesScala ? $e->seriesResponse->first() : $e->seriesResponse;
    }
}
