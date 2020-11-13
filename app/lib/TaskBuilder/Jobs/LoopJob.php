<?php

namespace App\Lib\TaskBuilder\Jobs;

use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\TaskBuilder;
use Illuminate\Support\Collection;
use Exception;

class LoopJob extends BaseJob
{
    public function handle(TaskEventer $e, $value)
    {
        // loop 内の task を生成する
        // TODO: try catch
        // function (TaskBuilder $builder): TaskBuilder
        $builder = TaskBuilder::builder($e->getEventManager());
        call_user_func($this->func, $builder);

        // 前処理 (引数を collection にする)
        $items = !($value instanceof Collection)
            ? collect($value)
            : $value;
        $e->fireEvent('before loop', $items);

        // ループの実行
        $res = $e->goDownLoopLevel(fn() => $this->loop($e, $builder, $items));

        // 後処理
        $e->fireEvent('after loop', $res);
        $e->clearRecords(['length', 'current', 'skip', 'success', 'throw']); // 統計を削除

        return $res;
    }

    protected function loop(TaskEventer $e, TaskBuilder $builder, Collection $values)
    {
        // イベント記録
        $length = $values->count();
        $e->writeRecord('length', $length);

        // iterator の取得
        $it = $values->getIterator();
        $it->rewind();

        // task を回す
        $buffer = collect(); // コンテナ (配列)
        while ($it->valid()) {
            $key = $it->key(); // キー (default は index値)
            $item = $it->current(); // 現在値
            $e->writeRecord('key', $key);
            $e->fireEvent('current', $item);

            // タスクの実行
            try {
                // 子イベントの実行
                $res = $e->goDownLoopLevel(fn() => $builder->handle($item, $e));

                if ($res === false) {
                    // skip 扱い
                    $e->incrementRecord('skip');
                    $e->fireEvent('skip', $res);
                } else {
                    // 正常終了
                    $e->incrementRecord('success');
                    $e->fireEvent('success', $res);
                    $buffer->push($res);
                }
            } catch (Exception $ex) {
                // error
                $e->incrementRecord('throw');
                $e->fireEvent('throw', $ex);
                // TODO: defaultException
                // throw $ex;
            }

            // 次へ
            $it->next();
        }

        // 統計を削除
        $e->clearRecords(['key']);

        return $buffer;
    }
}
