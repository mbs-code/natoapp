<?php

namespace App\Lib\TaskBuilder\Attrs;

use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\TaskBuilder;
use Illuminate\Support\Collection;
use Exception;

class LoopAttr extends BaseAttr
{
    public function handle(TaskEventer $e, $value)
    {
        // loop 内の task を生成する
        // TODO: try catch
        // function (TaskBuilder $builder): TaskBuilder
        $builder = TaskBuilder::builder();
        call_user_func($this->func, $builder);

        // 前処理 (引数を collection にする)
        $items = !($value instanceof Collection)
            ? collect($value)
            : $value;
        $e->fireEvent('before loop', $items, $this);

        // ループの実行
        $e->goDownNestLevel();
        $res = $this->loop($e, $builder, $items);
        $e->goUpNestLevel();

        // 後処理
        $e->fireEvent('after loop', $res, $this);

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
                $e->goDownNestLevel();
                $res = $builder->exec($item, $e); // event を引き継ぐ
                $e->goUpNestLevel();

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
                $e->fireEvent('throw', $res);
                // TODO: defaultException
                throw $ex;
            }

            // 次へ
            $it->next();
        }

        // 統計を削除
        $e->clearRecords(['length', 'key', 'current', 'skip', 'success', 'throw']);

        return $buffer;
    }
}
